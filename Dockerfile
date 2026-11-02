# syntax=docker/dockerfile:1

# GameSpin on Vercel
# -------------------
# Runs as a container-backed Vercel Function on FrankenPHP (PHP 8.4 + Caddy).
# The image is fully self-contained: Laravel, its vendor dependencies, and the
# compiled Vite/Tailwind frontend. Runtime configuration (APP_KEY, database,
# object storage, Steam, ...) is supplied through Vercel environment
# variables — see DEPLOYMENT.md.

ARG PHP_IMAGE=dunglas/frankenphp:1-php8.4-bookworm
ARG NODE_IMAGE=node:22-bookworm-slim

########################################################################
# Base: PHP 8.4 runtime + Composer + production extensions
########################################################################
FROM ${PHP_IMAGE} AS base

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pdo_pgsql intl zip opcache \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

########################################################################
# Stage 1: resolve PHP dependencies so the layers stay cacheable
########################################################################
FROM base AS vendor

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

########################################################################
# Stage 2: compile the frontend (Tailwind CSS 4 + Alpine.js + Vite 8)
########################################################################
FROM ${NODE_IMAGE} AS assets

WORKDIR /app
COPY .npmrc package.json package-lock.json ./

RUN npm ci

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# The Tailwind source list scans the framework's bundled pagination views
# (@source directive in resources/css/app.css), so ship them into this stage.
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views \
    ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views

RUN npm run build

########################################################################
# Stage 3: cache env-independent Laravel artifacts (views/routes)
#
# Never run `config:cache` here: Vercel injects secrets (APP_KEY, database,
# storage) at runtime, and baking them into the image would freeze whatever
# was available at build time.
########################################################################
FROM base AS app

WORKDIR /app
COPY --from=vendor /app /app
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/cache/locks \
        storage/framework/sessions \
        storage/framework/testing/disks \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && composer dump-autoload --no-dev --classmap-authoritative \
    && php artisan view:cache \
    && php artisan route:cache

########################################################################
# Stage 4: runtime image
#
# The base image already points at /etc/frankenphp/Caddyfile and has the
# CAP_NET_BIND_SERVICE capability on the binary, so binding port 80 as
# `www-data` works without any extra harness. We only install our config and
# make the Caddy data/config dirs writable for the PHP user.
########################################################################
FROM base AS runtime

WORKDIR /app
COPY --from=app --chown=www-data:www-data /app /app
COPY --chown=www-data:www-data Caddyfile /etc/frankenphp/Caddyfile

RUN chown -R www-data:www-data /config/caddy /data/caddy

ENV PORT=80 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    CACHE_STORE=database \
    SESSION_DRIVER=database \
    QUEUE_CONNECTION=sync \
    APP_MAINTENANCE_DRIVER=cache \
    APP_MAINTENANCE_STORE=database

USER www-data

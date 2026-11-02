# GameSpin on Vercel

GameSpin is designed to run as a **Vercel container-backed Function** running
FrankenPHP (PHP 8.4) with PostgreSQL, Redis-free database sessions, and
S3-compatible object storage for community post images.

The repository ships with everything needed:

| File | Role |
|---|---|
| `Dockerfile` | Multi-stage build: PHP 8.4 base, Composer, Vite/Tailwind assets, runtime image |
| `Caddyfile` | FrankenPHP/Caddy configuration: serves `public/`, feeds PHP into Laravel |
| `vercel.json` | Declares the container service and the catch-all rewrite |
| `.dockerignore` | Keeps secrets, caches and heavy files out of the image |
| `.vercelignore` | Keeps secrets and heavy files out of the Vercel upload |
| `config/demo.php` | Demo credentials read from environment variables |

`database/gamespin.sql` remains a **local MySQL convenience dump** and is not
used on Vercel.

---

## 1. Create the Neon PostgreSQL database

1. Create a Neon project at <https://neon.tech>.
2. Copy the **pooled connection string** (`...-pooler...`) — it is used at
   runtime by the container.
3. Copy the **direct (non-pooled) connection string** — it is used when you run
   `php artisan migrate` manually, because the pooler can drop DDL.

Both are stored as Vercel environment variables; the direct one is only read
from your machine when you run migrations.

```dotenv
DB_CONNECTION=pgsql
DB_URL="postgresql://user:password@ep-...-pooler.region.aws.neon.tech/neondb?sslmode=require"
# Separate, non-committed value used only for local CLI migrations:
DB_URL_DIRECT="postgresql://user:password@ep-...-nonpooler.region.aws.neon.tech/neondb?sslmode=require"
```

The application's `config/database.php` reads `DB_URL`; `DB_URL_DIRECT` is
purely a local helper documented below.

Laravel's default database config already supports `pgsq` connection
`sslmode=require`, so no extra PHP extension configuration is needed — but the
Docker image **must** include `pdo_pgsql` (it does).

---

## 2. Create the Cloudflare R2 bucket

1. Create a bucket in Cloudflare R2, e.g. `gamespin-uploads`.
2. Enable **public access** (or bind a custom domain) so image URLs resolve.
3. Create an **API token** with object read/write on that bucket.

Collect these values:

| Vercel env var | Meaning |
|---|---|
| `AWS_ACCESS_KEY_ID` | R2 access key (from the API token) |
| `AWS_SECRET_ACCESS_KEY` | R2 secret |
| `AWS_DEFAULT_REGION` | `auto` for R2 |
| `AWS_BUCKET` | bucket name |
| `AWS_ENDPOINT` | `https://<account-id>.r2.cloudflarestorage.com` |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` for R2 |
| `FILESYSTEM_PUBLIC_URL` | `https://pub-xxxxx.r2.dev` or your custom domain |
| `FILESYSTEM_PUBLIC_DRIVER` | `s3` |
| `FILESYSTEM_PUBLIC_THROW` | `true` (recommended) |

`Storage::disk('public')` in the application automatically uses these values.
No `php artisan storage:link` is required on Vercel.

---

## 3. Create the Vercel project

Two options:

1. **Git integration:** import the repository on <https://vercel.com/new>.
2. **CLI:**
   ```bash
   npm install -g vercel
   vercel login
   vercel link
   vercel deploy --prod
   ```

Set the framework preset to **Container** (`Dockerfile` is detected
automatically, and `vercel.json` forces the container runtime).

---

## 4. Environment variables (all set at runtime)

Run `vercel env add NAME` (or set them in the dashboard) for every variable
below. **Do not bake them into the image**, and **do not run
`php artisan config:cache`** inside the Docker build — Vercel injects secrets
after the container starts, so a build-time config cache would freeze the
build-time values.

### Application

| Variable | Value |
|---|---|
| `APP_NAME` | `GameSpin` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | `base64:...` — run `php artisan key:generate --show` to get one |
| `APP_URL` | `https://your-project.vercel.app` (or your custom domain, **no trailing slash**) |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `info` |

### Database

| Variable | Value |
|---|---|
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | Neon **pooled** connection string, `sslmode=require` |
| `DB_URL_DIRECT` | (local only) Neon **direct** connection string for migrations |

### Session / cache / queue

| Variable | Value |
|---|---|
| `SESSION_DRIVER` | `database` |
| `SESSION_LIFETIME` | `120` |
| `SESSION_SECURE_COOKIE` | `true` |
| `SESSION_SAME_SITE` | `lax` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `sync` (no queue workers are used today) |
| `APP_MAINTENANCE_DRIVER` | `cache` |
| `APP_MAINTENANCE_STORE` | `database` |
| `MAIL_MAILER` | `log` |
| `BROADCAST_CONNECTION` | `log` |

### Object storage (R2)

See section 2.

### Steam (optional)

| Variable | Value |
|---|---|
| `STEAM_API_KEY` | your Steam Web API key |
| `STEAM_CALLBACK_URL` | `https://<APP_URL>/steam/callback` |

If `STEAM_API_KEY` is blank, all Steam routes degrade gracefully and the seeded
demo library remains usable.

### Demo accounts

GameSpin defaults the demo emails to
`member@gamespin.test` / `admin@gamespin.test`. Outside local environments the
passwords **must** be supplied:

| Variable | Value |
|---|---|
| `DEMO_MEMBER_EMAIL` | optional (default kept) |
| `DEMO_MEMBER_PASSWORD` | strong password |
| `DEMO_ADMIN_EMAIL` | optional (default kept) |
| `DEMO_ADMIN_PASSWORD` | strong password |
| `DEMO_RESET_PASSWORDS` | `false` (never silently reset existing passwords) |

`config/demo.php` reads these values. Re-running `php artisan db:seed` never
overwrites an existing user's password unless `DEMO_RESET_PASSWORDS=true`.

---

## 5. Migrate and seed

Environment variables are injected by Vercel, so run migrations from your own
machine (or CI) with the **direct** Neon connection string:

```bash
cat > .env.production <<'EOF'
APP_ENV=production
APP_NAME=GameSpin
APP_KEY=use-the-same-key-as-the-container
DB_CONNECTION=pgsql
DB_URL=postgresql://...@ep-...-nonpooler...
EOF
php artisan migrate --env=production --force
php artisan db:seed --env=production --force
```

Repeat only when you change migrations or seeders. Migrations are *not* run at
container startup — see the official guidance on avoiding concurrent
installs across container instances.

---

## 6. Local Docker verification

You can exercise the exact production image before pushing to Vercel:

```bash
docker build -t gamespin .
docker run --rm -p 8080:80 \
    -e APP_KEY=$(php artisan key:generate --show) \
    -e APP_URL=http://localhost:8080 \
    -e APP_ENV=local \
    -e APP_DEBUG=true \
    -e DB_CONNECTION=pgsql \
    -e DB_URL="postgresql://...@localhost:5432/gamespin?sslmode=disable" \
    -e SESSION_DRIVER=database \
    -e CACHE_STORE=database \
    -e QUEUE_CONNECTION=sync \
    gamespin
```

Then visit <http://localhost:8080>.

`vercel dev` works too once `.env` and the Vercel project settings have been
pulled locally (`vercel env pull`).

---

## 7. Steam OpenID callback

Vercel terminates TLS and proxies to the container, so Laravel needs to trust
it — `bootstrap/app.php` already calls `trustProxies(at: '*')` for exactly that
reason. Also:

1. Set `APP_URL` to the same domain your users browse.
2. Set `STEAM_CALLBACK_URL` to an exact match (`https://<APP_URL>/steam/callback`).
3. Disable Vercel Deployment Protection on the production environment, or the
   OpenID verification redirect will be intercepted.
4. Steam only succeeds if the returned profile is public. If the profile is
   private, the sync reports a clear failure instead of wiping your library.

---

## 8. What changes when you redeploy

| Change | Effect |
|---|---|
| Code or dependencies | Vercel rebuilds the Docker image |
| `resources/views/*.blade.php` | `view:cache` runs at build; runtime uses the fresh cache |
| Vite/Tailwind source | `npm run build` runs in the Docker build |
| Environment variables | applied on the next deploy; no rebuild needed (only a redeploy) |
| `database/gamespin.sql` | unaffected (local MySQL convenience dump) |

---

## 9. Troubleshooting

| Symptom | Fix |
|---|---|
| `502` / black screen | The container didn't start. Check Vercel runtime logs; verify the `Caddyfile` was copied over the image's default config (`/etc/frankenphp/Caddyfile`). |
| `no encrypted cookies` / `APP_KEY` | Set `APP_KEY`, then redeploy. |
| `No application encryption key` | See the previous row. |
| `VITE manifest not found` | Vite build output missing from `public/build`; check Dockerfile's assets stage. |
| Images fail to appear | `FILESYSTEM_PUBLIC_DRIVER=s3` missing, R2 public domain not set, or wrong `AWS_*` values. |
| Steam callback loop | Deployment Protection not disabled, `STEAM_CALLBACK_URL` mismatch with `APP_URL`, or a private Steam profile. |
| Sync never progresses | Sync rows can time out; `hasActiveRun()` automatically reaps stale runs and unlocks the next attempt. |
| Migrations fail | Use the non-pooled Neon connection; the pooler may reject DDL. |

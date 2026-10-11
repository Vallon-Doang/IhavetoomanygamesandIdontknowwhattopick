# GameSpin

GameSpin is a Laravel + MySQL web application that helps you decide which game to play.
Members build a game library (seeded demo data or a linked Steam account), filter the
eligible games, and let a fair server-side roulette pick one at random. Every pick is
recorded in a personal history. Administrators get a separate dashboard with read-only
reporting on users, the shared game catalog, and Steam sync activity.

This repository is a school web-application project and is designed to be reproducible
from a fresh Git clone.

## Features

- Public landing page with registration / login.
- Session authentication with hashed passwords, CSRF protection and login rate limiting.
- Two roles on a single `users` table: **member** and **admin** (admin is never granted by public registration).
- Member dashboard: game count, exclusions, Steam status, last and recent picks.
- Game library with title search, playtime and exclusion toggles.
- Server-side roulette picker with optional title/playtime filters; the browser only animates the already-chosen result.
- Spin history, newest first, scoped to the signed-in member.
- Optional Steam OpenID account linking and owned-games synchronization, with graceful handling of missing credentials / private libraries.
- Admin dashboard, user directory, game catalog and sync activity, all backed by MySQL counts.

## Requirements (tested versions)

| Software | Version used for testing |
|---|---|
| PHP | 8.3.32 (project requires `^8.3`) |
| Composer | 2.10.3 |
| Node.js | 22.23.1 |
| npm | 10.9.8 |
| MySQL | MySQL 8+ recommended — tested against MariaDB 11.8.8 (MySQL-compatible) |
| Laravel | 13.x |

Required PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`,
`json`, `bcmath`, `fileinfo`, `curl`, `dom`, `zip`.

## Fresh-clone installation

> For step-by-step setup on a brand-new machine (Windows-first, plus Linux), see
> [`INSTALL.md`](INSTALL.md).

```bash
git clone <repository-url>
cd ihavenogameandimustlibrary
composer install
npm ci
cp .env.example .env
php artisan key:generate
```

On Windows (PowerShell), copy the environment file with:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### Configure the database

Create an empty MySQL database, then set the connection in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gamespin
DB_USERNAME=gamespin
DB_PASSWORD=your-password
```

### Create the schema and demo data

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Then open <http://127.0.0.1:8000>.

`php artisan migrate --seed` is the primary way to create the database: migrations define
the schema and seeders insert repeatable demo data. Seeders are safe to re-run without
duplicating users or games.

### Run and build

```bash
php artisan serve          # start the development server
npm run build              # compile Tailwind CSS + Alpine.js for production
npm run dev                # optional: Vite dev server with hot reload
```

## Local demo credentials

Both accounts use the password `password`. These are **local-demo-only** and must be
changed or removed in any real deployment.

| Role | Email | Password |
|---|---|---|
| Member | `member@gamespin.test` | `password` |
| Admin | `admin@gamespin.test` | `password` |

### Suggested demo workflow

1. Open the landing page and register a brand-new account (it is created as a **member**).
2. Log in as the seeded member to see the dashboard and the seeded demo library.
3. Search or exclude a game in **Library**, then use **Roulette** to spin and confirm the pick appears in **History**.
4. Log out and log in as the seeded admin; open **Admin → Users / Games / Syncs**.
5. Confirm a member cannot open `/admin` (HTTP 403).

## Steam linking and synchronization

Steam features are optional; the roulette works entirely from seeded demo data.

To enable genuine Steam integration, set server-side credentials in `.env` (never commit them):

```dotenv
STEAM_API_KEY=your-steam-web-api-key
STEAM_CALLBACK_URL="${APP_URL}/steam/callback"
```

- **Link:** a signed-in member visits `/steam/connect`, is sent to Steam OpenID, and returns to `/steam/callback`. The returned identity is verified server-side; a SteamID typed by a user is never trusted, and a SteamID already linked to another user is rejected.
- **Sync:** `POST /steam/sync` imports the member's owned games into the shared `games` catalog and their `user_games` ownership rows. Repeated syncs are idempotent (upsert by Steam App ID and by user/game).
- **Failures are non-destructive:** a failed or incomplete sync keeps the last valid library and records a failed `steam_sync_runs` row. A private or unavailable library is reported as an explicit user-facing state, not treated as "zero games".

### Fallback without Steam

If `STEAM_API_KEY` is empty, `/steam/connect` and `/steam/sync` show a helpful message and
the seeded demo library remains fully usable. This is the expected state for the standard
demo presentation.

## Architecture overview

Single Laravel monolith — Blade views styled with Tailwind CSS v4, with Alpine.js used for
the roulette interaction on the picker page.

- `app/Http/Controllers` — request handling for public, member and `Admin\*` areas.
- `app/Http/Requests` — form-request validation (registration, login, spin filters).
- `app/Http/Middleware/EnsureUserIsAdmin.php` — the `admin` route middleware.
- `app/Policies/UserGamePolicy.php` — ownership checks for library records.
- `app/Services` — `SteamService`, `SteamLibrarySyncService`, `GamePickerService`.
- `app/Models` — `User`, `SteamAccount`, `Game`, `UserGame`, `SpinHistory`, `SteamSyncRun`.

### Main routes

| Method + URL | Access | Purpose |
|---|---|---|
| `GET /` | Public | Landing page |
| `GET/POST /register`, `GET/POST /login`, `POST /logout` | Guest / auth | Authentication |
| `GET /dashboard` | Member | Dashboard |
| `GET /library`, `PATCH /library/{userGame}/exclusion` | Member | Library + exclusion toggle |
| `GET /picker`, `POST /picker/spin` | Member | Roulette |
| `GET /history` | Member | Spin history |
| `GET /steam/connect`, `GET /steam/callback`, `POST /steam/sync`, `DELETE /steam/disconnect` | Member | Steam |
| `GET /admin`, `/admin/users`, `/admin/games`, `/admin/syncs` | Admin | Administration |

## Running the tests

Tests run against an in-memory SQLite database and mock the Steam API, so no network or
MySQL is required:

```bash
php artisan test
```

Covered: guest pages, member-only registration, role-based access, picker eligibility and
history rules, cross-user isolation, and idempotent / failure-safe Steam sync.

## Troubleshooting

- **Database connection error:** make sure the MySQL service is running and the `DB_*` values in `.env` are correct. Create the database before migrating.
- **`could not find driver` / missing extension:** install and enable `pdo_mysql` (and the other extensions listed above).
- **Migration failure after partial setup:** recreate an empty database, then run `php artisan migrate:fresh --seed`.
- **Missing styles or "Vite manifest not found":** run `npm ci && npm run build`.
- **Steam API failure / empty library:** confirm `STEAM_API_KEY` is set and the Steam profile's game details are public. Without a key, use the seeded demo library.
- **Permissions on `storage/`:** the web server needs write access to `storage/` and `bootstrap/cache/`.

## Attribution

Built with [Laravel](https://laravel.com), [Tailwind CSS](https://tailwindcss.com) and
[Alpine.js](https://alpinejs.dev). Seeded Steam cover images are referenced from the Steam
CDN (`cdn.cloudflare.steamstatic.com`); the application renders fine if they are unavailable.
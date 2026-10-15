# GameSpin

GameSpin is a Laravel + MySQL web application that helps you decide which game to play.
Members build a game library (seeded demo data or a linked Steam account), filter the
eligible games, and let a fair server-side roulette pick one at random. Every pick is
recorded in a personal history. Members can also post to a community feed, and
administrators get a separate dashboard for moderation, announcements, analytics and
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
- Community feed with image posts; authors can edit/delete their own posts, and post creation validates text and uploads (JPG/PNG/WebP, max 2 MB).
- Admin post moderation (hide with a reason / restore / permanently delete) and admin announcements that surface at the top of the feed.
- Admin analytics page with live MySQL figures (users, posts, hidden posts, spins, a 14-day post chart, top games and top posters).
- Administrators can unlink a user's Steam account (logged), and members who hit a Steam-linking conflict can open a reset request that admins resolve or dismiss.

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
php artisan storage:link   # required for community post image uploads
npm run build
php artisan serve
```

Then open <http://127.0.0.1:8000>.

`php artisan migrate --seed` is the primary way to create the database: migrations define
the schema and seeders insert repeatable demo data. Seeders are safe to re-run without
duplicating users, games, posts or announcements.

`php artisan storage:link` creates the public symlink used to serve uploaded post images.
Community posts without images work fine without it.

### Alternative: import the provided SQL dump

If the teacher requires an importable SQL file (for example through **phpMyAdmin**), a
version-matched dump is included at [`database/gamespin.sql`](database/gamespin.sql).

**phpMyAdmin**

1. Create an empty database (for example `gamespin`) and select it in the sidebar.
2. Open the **Import** tab, choose `database/gamespin.sql`, and click **Import**.
3. Confirm the tables and demo rows appear (users, games, spin history, posts, announcements).

**Command line**

```bash
mysql -u gamespin -p gamespin < database/gamespin.sql
```

The dump contains the schema, the two demo accounts (documented local-demo-only passwords)
and the seeded games, spins, posts and announcements. It holds no real user data or secrets.
Use **either** the dump **or** `php artisan migrate --seed` against an empty database —
never both on the same tables. Regenerate the dump after schema or seed changes:

```bash
mysqldump -h 127.0.0.1 -u gamespin -p --no-tablespaces --default-character-set=utf8mb4 gamespin > database/gamespin.sql
```

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
4. Open **Community**, create a post (optionally with an image), then edit it. Notice the seeded announcements at the top of the feed.
5. Log out and log in as the seeded admin; open **Admin → Users / Games / Syncs**.
6. In **Admin → Moderation**, hide a post with a reason (it disappears from the feed) and then restore it. In **Admin → Announcements**, publish one and confirm it appears at the top of the member feed.
7. Open **Admin → Analytics** to review live MySQL figures.
8. Confirm a member cannot open `/admin` or edit another member's post (HTTP 403).
9. In **Admin → Users**, use **Unlink Steam** to clear a user's Steam link, or resolve a pending ticket in **Admin → Steam requests**.

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
- **One Steam account per user / one user per SteamID:** linking a SteamID already attached to another GameSpin account is rejected. If a member believes that is a mistake, their dashboard offers a **Request a Steam reset** button that opens a ticket; an administrator can resolve it (which unlinks the conflicting Steam account) or dismiss it from **Admin → Steam requests**. Admins can also unlink any user's Steam account from **Admin → Users**.

### Fallback without Steam

If `STEAM_API_KEY` is empty, `/steam/connect` and `/steam/sync` show a helpful message and
the seeded demo library remains fully usable. This is the expected state for the standard
demo presentation.

## Architecture overview

Single Laravel monolith — Blade views styled with Tailwind CSS v4, with Alpine.js used for
the roulette interaction on the picker page.

- `app/Http/Controllers` — request handling for public, member and `Admin\*` areas (including `CommunityController` and the admin moderation/announcement/analytics controllers).
- `app/Http/Requests` — form-request validation (registration, login, spin filters, posts, announcements, moderation).
- `app/Http/Middleware/EnsureUserIsAdmin.php` — the `admin` route middleware.
- `app/Policies` — `UserGamePolicy` (library ownership), `PostPolicy` (author-only edits; admin moderation) and `AnnouncementPolicy` (admin-only).
- `app/Services` — `SteamService`, `SteamLibrarySyncService`, `GamePickerService`.
- `app/Models` — `User`, `SteamAccount`, `Game`, `UserGame`, `SpinHistory`, `SteamSyncRun`, `Post`, `Announcement`.

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
| `POST /steam/reset-request` | Member | Request help with a conflicting Steam link |
| `GET /community`, `POST /community/posts`, `GET /community/posts/{post}/edit`, `PUT/DELETE /community/posts/{post}` | Member | Community feed + own-post editing |
| `GET /admin`, `/admin/users`, `/admin/games`, `/admin/syncs` | Admin | Administration |
| `GET /admin/posts`, `PATCH /admin/posts/{post}/moderation`, `DELETE /admin/posts/{post}` | Admin | Post moderation |
| `GET /admin/announcements`, `POST /admin/announcements`, `GET/PUT/DELETE /admin/announcements/{announcement}` | Admin | Announcements |
| `GET /admin/analytics` | Admin | Community analytics |
| `DELETE /admin/users/{user}/steam` | Admin | Unlink a user's Steam account (logged) |
| `GET /admin/steam-requests`, `PATCH /admin/steam-requests/{steamResetRequest}` | Admin | Review and resolve/dismiss Steam reset requests |

## Running the tests

Tests run against an in-memory SQLite database and mock the Steam API, so no network or
MySQL is required:

```bash
php artisan test
```

Covered: guest pages, member-only registration, role-based access, picker eligibility and
history rules, cross-user isolation, idempotent / failure-safe Steam sync, community post
creation and validation, author-only editing, upload validation, admin moderation,
announcements and analytics access, and Steam conflict / reset-request handling.

## Troubleshooting

- **Database connection error:** make sure the MySQL service is running and the `DB_*` values in `.env` are correct. Create the database before migrating.
- **`could not find driver` / missing extension:** install and enable `pdo_mysql` (and the other extensions listed above).
- **Migration failure after partial setup:** recreate an empty database, then run `php artisan migrate:fresh --seed`.
- **Missing styles or "Vite manifest not found":** run `npm ci && npm run build`.
- **Steam API failure / empty library:** confirm `STEAM_API_KEY` is set and the Steam profile's game details are public. Without a key, use the seeded demo library.
- **Community post images not loading (404):** run `php artisan storage:link` so `public/storage` points to `storage/app/public`.
- **Permissions on `storage/`:** the web server needs write access to `storage/` and `bootstrap/cache/`.

## Attribution

Built with [Laravel](https://laravel.com), [Tailwind CSS](https://tailwindcss.com) and
[Alpine.js](https://alpinejs.dev). Seeded Steam cover images are referenced from the Steam
CDN (`cdn.cloudflare.steamstatic.com`); the application renders fine if they are unavailable.
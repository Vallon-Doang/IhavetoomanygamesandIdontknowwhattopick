# GameSpin — System Design Specification

**Project type:** School web-application project  
**Status:** Implementation specification (v1.0)  
**Backend:** Laravel / PHP  
**Database:** MySQL  
**Frontend:** Blade, Tailwind CSS, Alpine.js (where useful)

## 1. Overview

GameSpin is a Laravel web application that helps a user decide which game to play. A member can connect a Steam account, synchronize an accessible Steam game library, filter eligible games, and randomly pick a game through a roulette-style interface. The app records selection history. Administrators can review users, game records, synchronization activity, and application health.

The school submission must be **reproducible from a fresh Git clone**: install dependencies, create an empty MySQL database, migrate/seed it, run the site, and demonstrate all mandatory features. The main picker must remain demonstrable with seeded sample data if Steam credentials or live network access are unavailable.

### 1.1 School requirements (mandatory)

1. Use **Laravel** and **MySQL**.
2. The repository can be cloned onto a new device and installed using its README.
3. A new database can be created/imported and populated reproducibly.
4. The website can be presented with a stable demo flow.
5. Registration and login work.
6. **Member** and **Admin** have distinct, authorization-protected pages.
7. A public landing page exists.
8. A README documents prerequisites, installation, configuration, database setup, and demo usage.

### 1.2 Scope

**Must have for submission:** landing page; registration/login/logout; member and admin role protection; member dashboard; seeded game library; working roulette picker and history; admin dashboard; migrations/seeders; README; a fresh-clone verification procedure.

**Core GameSpin feature, after school basics are stable:** Steam account linking and owned-games synchronization, with clear handling of unavailable/private libraries.

**Optional/stretch:** favorite games, tags/genres, sophisticated recommendations, statistics charts, scheduled periodic synchronization, social features, extensive admin tooling. Do not implement these ahead of the mandatory requirements.

### 1.3 Non-goals

- No microservices, separate JavaScript frontend, real-time infrastructure, or unnecessary third-party packages.
- No purchases, Steam trading, or launching/installing games remotely.
- No assumption that Steam is always reachable or a game library is always public.
- No production hosting requirement unless the teacher later specifies one.

## 2. Technology and Architecture

| Layer | Selected technology | Responsibility |
|---|---|---|
| Server | Laravel / PHP | Routes, authorization, validation, business logic |
| Data | MySQL with Eloquent | Users, Steam accounts, games, ownership, history, sync runs |
| Views | Blade + Tailwind CSS | Responsive pages and components |
| Interactivity | Alpine.js (optional) | Filters, roulette animation, UI interactions |
| Authentication | Laravel's supported session authentication | Registration, login, logout, password hashing |
| Background work | Laravel Jobs / Queue (optional initially) | Steam synchronization when requests are long |
| External API | Steam Web API / Steam OpenID | Steam identity linking and accessible game library data |

Use a **single Laravel monolith**. Keep domain boundaries logical, not as separate deployable services.

```text
Browser (Blade + optional Alpine.js)
            |
       Laravel routes
            |
Controllers + Form Requests + Policies/Middleware
            |
      Application services
       /       |       \
 SteamService LibraryService GamePickerService
       \       |       /
       Eloquent Models
            |
          MySQL

SteamService <----> Steam services (external)
```

### 2.1 Responsibilities

- **Controllers:** receive requests, call validation and services, return views or redirects.
- **Form Requests:** validate and authorize incoming data where appropriate.
- **Services:** Steam communication, library synchronization, eligible-game selection, history recording.
- **Models:** relationships and persistence, not remote API calls.
- **Middleware / Policies:** enforce authentication and role/ownership access server-side.
- **Jobs:** queue long Steam imports if needed; don't block normal page navigation.
- **Blade components:** reusable navigation, alerts, cards, and form controls.

Suggested service classes (names are guidance, not required conventions): `SteamService`, `SteamLibrarySyncService`, `GamePickerService`.

## 3. Actors and Permissions

### 3.1 Guest

Can view the landing page, register, and log in. Cannot access member or admin dashboards.

### 3.2 Member

Can view their dashboard, link/unlink their own Steam account, synchronize their own accessible library, view their own games, filter and spin, and view their own selection history. Cannot access other users' records or admin routes.

### 3.3 Admin

Can access a separate admin dashboard and inspect/manage registered users, shared game catalog records, and sync statuses/logs. For a school project, start with **read-only reporting plus limited user status management**; avoid destructive operations unless documented and protected. Admin privileges are assigned through an authorized seeder or admin process, **never through public registration**.

### 3.4 Permission matrix

| Capability | Guest | Member | Admin |
|---|:---:|:---:|:---:|
| Landing page | Yes | Yes | Yes |
| Register / login | Yes | Login only if signed out | Login only if signed out |
| Member dashboard/library/picker/history | No | Own data | No by default |
| Link/sync Steam | No | Own account | No by default |
| Admin dashboard | No | No | Yes |
| User and sync administration | No | No | Yes |

Use one `users` table and the same Laravel session-authentication system for both roles. After login, redirect based on role. Route separation does **not** replace authorization checks.

## 4. Pages and Routes

Exact route names can be adapted to existing Laravel conventions; the behaviors and authorization must remain consistent.

| Method + URL | Page or action | Access | Behavior |
|---|---|---|---|
| `GET /` | Landing | Public | Explain GameSpin; login/register calls to action |
| `GET /register` / `POST /register` | Registration | Guest | Create a `member` user |
| `GET /login` / `POST /login` | Login | Guest | Authenticate and redirect by role |
| `POST /logout` | Logout | Authenticated | Invalidate session securely |
| `GET /dashboard` | Member dashboard | Member | Steam status, game count, recent spins |
| `GET /library` | Game library | Member | List/search/filter only member's games |
| `GET /picker` | Roulette | Member | Configure filters and show selection UI |
| `POST /picker/spin` | Choose game | Member | Validate filters, pick server-side, save history |
| `GET /history` | Spin history | Member | View user's previous picks |
| `GET /steam/connect` | Start Steam link | Member | Redirect to trusted Steam identity flow |
| `GET /steam/callback` | Complete link | Member | Verify returned identity, bind SteamID |
| `POST /steam/sync` | Import library | Member | Start sync, guard against concurrent jobs |
| `DELETE /steam/disconnect` | Unlink Steam | Member | Unlink identity according to retention policy |
| `GET /admin` | Admin dashboard | Admin | Counts and system summary |
| `GET /admin/users` | User directory | Admin | List/search users and roles |
| `GET /admin/games` | Game catalog | Admin | List games and Steam app IDs |
| `GET /admin/syncs` | Sync activity | Admin | Last runs, results and failures |

Admin pages can share the same login screen but must have clearly separate layout/navigation and route prefix. Additional create/update/delete admin routes need explicit authorization and validation if added.

## 5. Database Design

All tables must be created through Laravel migrations. Use foreign keys, indexes, appropriate nullability, and uniqueness constraints. The structures below define the required logical model; choose framework-compatible column types during implementation.

### 5.1 `users`

| Column | Purpose |
|---|---|
| `id` (PK) | Internal user identifier |
| `name` | Display name |
| `email` (unique) | Login identifier |
| `password` | Laravel-hashed password |
| `role` | `member` or `admin`, default `member` |
| `created_at`, `updated_at` | Timestamps |

Use Laravel's standard remember-token/email-verification fields only if the chosen auth starter needs them. Public registration **must not accept a role parameter**.

### 5.2 `steam_accounts`

| Column | Purpose |
|---|---|
| `id` (PK) | Record ID |
| `user_id` (FK, unique) | Linked local account |
| `steam_id` (unique) | Verified SteamID64 |
| `display_name` (nullable) | Last-known public display name |
| `avatar_url` (nullable) | Last-known avatar |
| `last_synced_at` (nullable) | Time of last successful sync |
| `created_at`, `updated_at` | Timestamps |

One Steam account per local user for v1. Linking must use a **verified Steam identity response**, not an arbitrary SteamID typed by a user. Credentials for calling Steam APIs belong on the server, not in this table or in browser code.

### 5.3 `games`

| Column | Purpose |
|---|---|
| `id` (PK) | Internal game identifier |
| `steam_app_id` (unique, nullable) | Steam catalog ID; nullable for seeded demo-only games |
| `name` | Game title |
| `cover_url` (nullable) | Artwork URL |
| `created_at`, `updated_at` | Timestamps |

A shared catalog avoids duplicating a game title for every user. Seeded games can be marked as demo via a `source` enum/string if a separate source flag is needed. Do not depend on third-party art being available to render the library.

### 5.4 `user_games`

| Column | Purpose |
|---|---|
| `id` (PK) | Ownership row ID |
| `user_id` (FK) | Member |
| `game_id` (FK) | Catalog game |
| `playtime_minutes` (default 0) | Playtime reported by Steam or seed data |
| `source` | `steam` or `demo` |
| `is_excluded` (default false) | Exclude from picker |
| `last_seen_at` (nullable) | Last time item was present during Steam sync |
| `created_at`, `updated_at` | Timestamps |

Add a composite unique index on `(user_id, game_id)`. A game may belong to multiple users. Decide how to handle removed/private Steam games: **do not delete library entries blindly on failed or incomplete syncs**. For v1, retain previous records and display a sync warning.

### 5.5 `spin_history`

| Column | Purpose |
|---|---|
| `id` (PK) | Selection ID |
| `user_id` (FK) | Member who spun |
| `game_id` (FK) | Selected game |
| `filters` (nullable JSON) | Applied filter settings snapshot |
| `created_at` | Selection time |

History must be queried by authenticated `user_id` and ordered newest-first. Prefer retaining the selected game's display name snapshot if administrators may later rename/delete catalog entries. Do not expose history of another user.

### 5.6 `steam_sync_runs`

| Column | Purpose |
|---|---|
| `id` (PK) | Sync run ID |
| `user_id` (FK) | Initiating member |
| `status` | `pending`, `running`, `success`, or `failed` |
| `games_imported` (default 0) | Upserted/linked records count |
| `error_message` (nullable) | Sanitized error summary, no API keys |
| `started_at`, `finished_at` (nullable) | Timing |
| `created_at`, `updated_at` | Timestamps |

This table is useful but may be simplified for a tight deadline as long as sync status/errors can be demonstrated.

### 5.7 Relationships

```text
users (1) -------- (0..1) steam_accounts
users (1) -------- (many) user_games (many) -------- (1) games
users (1) -------- (many) spin_history (many) ------ (1) games
users (1) -------- (many) steam_sync_runs
```

Use indexed foreign keys. Define deletion behavior intentionally: deleting a user can cascade to their account links, ownership and history; deleting a game referenced by history should normally be restricted or handled with archived snapshots rather than silently losing history.

## 6. Authentication and Security

1. Use a maintained Laravel session-authentication approach compatible with the installed Laravel version.
2. Hash passwords through Laravel's hashing mechanism.
3. Validate user inputs, including registration, filters, sync requests, and any admin changes.
4. Enforce CSRF protection on state-changing web forms.
5. Restrict pages and actions through `auth`, role middleware, and ownership checks/policies as needed.
6. Rate-limit login attempts and Steam synchronization requests.
7. Never expose the Steam Web API key or `.env` secrets in browser output or repository history.
8. Regenerate the session after login; invalidate it on logout per Laravel guidance.
9. Escape user-supplied strings in templates (Blade's escaped output by default).
10. Do not enable public admin registration or accept `role=admin` from request payloads.

## 7. Steam Account Connection and Sync

### 7.1 Account connection

Use Steam's supported OpenID sign-in/identity verification flow for linking a SteamID to an already authenticated GameSpin member. Verify the callback with the provider; do **not** consider query-string SteamID values proof of identity. Prevent another local user from linking a SteamID that is already associated with an account. Steam identity linking is different from authorizing unrestricted library access.

### 7.2 Import flow

```text
Member requests sync
  -> Validate authenticated linked Steam account
  -> Reject/skip concurrent sync for same account
  -> Create sync run record
  -> Request owned games from Steam using server-side configuration
  -> Validate successful API response
  -> Upsert games by steam_app_id
  -> Upsert user_games by (user_id, game_id)
  -> Update last successful sync timestamp
  -> Mark sync run success and show game count
```

Failures must preserve the last valid local library and produce a friendly status, not a blank page. Steam library visibility and API limitations may prevent retrieval. Treat a missing/private/inaccessible library as an explicit user-facing state; do not assume an empty response proves the user owns zero games.

### 7.3 Background processing

For small libraries, synchronous implementation may be sufficient initially. If requests become slow or time out, move synchronization into a queued job. Queue and scheduler infrastructure are **optional**, not prerequisites for demonstrating the seeded game picker. Avoid configuring an unsupervised queue driver that requires a worker but leaves jobs permanently pending on the teacher's computer.

### 7.4 Configuration

Place required Steam settings in `.env.example` as empty placeholders, e.g. `STEAM_API_KEY=` and any callback URL variables actually used. Read them through Laravel configuration files (`config/services.php` or equivalent), not scattered direct environment lookups.

## 8. Game Library and Picker

### 8.1 Library

Members see only games in their `user_games` relationship. Show title, optional artwork, playtime, and excluded/included status. Support basic title search and exclusion toggle. A seeded account includes enough demo games to test functionality without Steam.

### 8.2 Picker rules

- Eligible games belong to the authenticated member and are not excluded.
- Optional v1 filters: title search and minimum/maximum playtime. Apply only filters implemented and exposed in the UI.
- If no game matches, show a helpful empty state and do not write an invalid history row.
- All eligible games have equal probability; no hidden weighting.
- Select the winning game **on the Laravel backend**, then record it once in `spin_history`.
- The browser animates the already-selected result; the animation must never silently choose a different winner.
- Prevent duplicate history records from accidental repeated submission (disable button while pending; optionally use an idempotency token).
- After success, show selected title and a route back to history/library.

### 8.3 Presentation behavior

The roulette can be a simple animated carousel/spinner using Blade and Alpine.js. Prefer a stable, accessible implementation over complex animation. With JavaScript limited or disabled, a normal form submission should still produce a selected game if feasible.

## 9. UI/UX Requirements

- **Landing page:** name, brief purpose, how it works, login/register call to action.
- **Auth pages:** labeled fields, helpful validation feedback, appropriate redirects.
- **Member dashboard:** welcome, number of games, link/sync status, last picked game, navigation.
- **Library:** search, clear no-games state, exclusions, status of sync/demo games.
- **Picker:** clear spin action, result, accessible loading/errors, filters if implemented.
- **History:** newest-first list of selected games and timestamps.
- **Admin dashboard:** user/game/sync counts and links to admin data pages.
- **Admin users/games/syncs:** basic tables with readable states and pagination when useful.

Provide responsive layouts, consistent navigation, and distinct member/admin labeling. Don't use fake counters represented as real data. Page metrics must derive from MySQL.

## 10. Installation, Portability, and Repository

### 10.1 Repository contents

Required tracked files include `composer.json`, `composer.lock`, `package.json`, frontend lockfile, Laravel source, migrations, seeders, views, routes, `vite.config.*` if used, `.env.example`, and `README.md`. Keep `.env`, vendor dependencies, `node_modules`, compiled caches and secrets out of Git. Commit only assets/source required to rebuild.

### 10.2 Preconditions

The README must state the **actual versions used** of PHP, Composer, Node.js/npm, MySQL, and required PHP extensions. Do not assert version numbers until verified against the project.

### 10.3 Fresh clone flow

Commands below are the expected general workflow; the README must tailor them to the final repository and target environment.

```bash
git clone <repository-url>
cd <repository-directory>
composer install
npm ci
cp .env.example .env
php artisan key:generate
```

Next: create an empty MySQL database, configure `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env`, and run:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

`cp` is Unix/macOS syntax; provide a Windows alternative if demonstrating on Windows. Document any required local MySQL service startup. If using filesystem uploads, execute `php artisan storage:link` only when actually needed. If queues are required for Steam sync, document exactly how to start a worker or provide a safe synchronous/demo fallback.

### 10.4 Database import

**Primary recommended approach:** run migrations and seeders against a newly created empty MySQL database. This creates a version-controlled schema and repeatable demo data. If the teacher specifically requires a SQL file, include a sanitized, version-matched SQL export **as an alternative**, with import instructions. Never require both SQL import and migrations against the same already-created tables.

### 10.5 Demo seeders

- Create one **Member** and one **Admin** account with deterministic, documented **local-demo-only** credentials.
- Create a representative sample of games and link several to the Member using `source=demo`.
- Optionally seed a few spin-history records for display, but the live picker must create a new record during presentation.
- Seeders should be safe to rerun without duplicating users or games (use stable unique keys and upsert/update-or-create operations).
- Do not distribute real user data, Steam API keys, real passwords, or private profile data in seeders.
- Clearly warn that demo passwords must be changed/removed in any real deployment.

## 11. README Specification

`README.md` must include:

1. Project description and screenshots (optional but useful).
2. Requirements and tested software versions.
3. Fresh-clone installation commands.
4. How to configure MySQL and `.env` (including where API keys go, without publishing values).
5. How to migrate/seed or alternatively import a provided SQL dump.
6. Commands to run the server and build assets.
7. Local demo credentials and each role's test workflow.
8. How Steam linking and syncing work, plus limitations and fallback when Steam is unavailable.
9. Common troubleshooting: database connection, missing PHP extension, migration failure, missing build output, Steam API failure.
10. Brief architecture or route overview and attribution for any external assets.

## 12. Test Plan and Acceptance Criteria

### 12.1 Automated tests (preferred)

- Guest can load landing, registration, and login screens.
- Public registration always creates a member, even if a forged admin role is submitted.
- Member can log in, reach member pages, and cannot access admin routes.
- Admin can log in, reach admin pages, and cannot accidentally access another user's private data through member endpoints.
- Unauthenticated requests cannot spin or sync.
- Picker chooses only eligible owned games and records one history item.
- Picker handles empty eligible set without writing history.
- A member cannot view another member's library/history or trigger another member's Steam sync.
- Repeated sync/import does not duplicate catalog/ownership records (use mocked API data for testing).
- Steam sync failure does not remove existing games.

### 12.2 Manual fresh-install acceptance test

On a different computer or clean environment:

1. Clone the repository.
2. Install Composer and npm dependencies.
3. Copy/configure `.env`; generate application key.
4. Create an empty MySQL database.
5. Run `php artisan migrate --seed` without errors.
6. Build and start the application using README commands.
7. View the landing page and register a new member.
8. Log in using seeded member credentials and view seeded games.
9. Spin and confirm history stores the selected game.
10. Log in as seeded admin and open the admin dashboard.
11. Confirm member cannot enter `/admin`.
12. If Steam credentials are absent, confirm the demo still functions and Steam features show a helpful message.

**Definition of done:** all mandatory school requirements pass on a clean installation, with no reliance on the original developer's database or private environment files.

## 13. Presentation Script

1. Open the public landing page; introduce GameSpin's purpose.
2. Show registration, then log in as the seeded member.
3. Show the member dashboard and MySQL-backed game library.
4. Demonstrate a filter/exclusion, spin once, and confirm the selected game appears in history.
5. Show optional Steam connection/sync if available; otherwise explain the seeded demo fallback.
6. Log out; log in as admin and show separate dashboard, users, game catalog, and sync status.
7. Show that member/admin permissions are enforced (no unauthorized admin access).
8. Briefly show the README, migrations, seeders, and fresh-clone installation workflow.

## 14. Development Priorities

Implement in this order:

**Phase A — School baseline:** Laravel project, MySQL configuration, migrations, authentication, roles, landing/member/admin pages.

**Phase B — Local GameSpin:** seeded games, member library, picker, and history, including tests.

**Phase C — Steam integration:** verified account linking, sync service, error states, retry/queue only if needed.

**Phase D — Submission readiness:** README, install validation on a clean device, demo walkthrough, visual polish, targeted fixes.

Do not hold up Phases A/B for Steam integration or optional features.

## 15. Assumptions and Decisions to Confirm

These are **design choices proposed for the school project**, not requirements explicitly provided by the teacher:

- One Laravel session-authentication system and one `users` table with `member`/`admin` role values.
- One linked Steam account per member.
- Seeded demo games are allowed and do not pretend to be actual Steam ownership.
- A basic admin directory is sufficient; user deletion/editing is not necessary to pass the assignment.
- Roulette uses equal-probability selection, not weighted recommendations.
- Migrations/seeders are the main method of creating/importing the database; SQL export is optional unless demanded by the rubric.
- Queue workers and scheduled synchronization are optional rather than mandatory infrastructure.

If the actual grading rubric conflicts with these choices, update this specification before implementation.

---

**Companion document:** `DEVELOPMENT_PROMPT.md` should direct the coding agent to read this specification first, inspect the existing project, implement incrementally, and avoid adding technology that isn't needed.

# GameSpin — Fresh Device Installation Guide

This guide installs GameSpin on a brand-new machine. It is written **Windows-first**
(the main device), with a **Linux** section for testing/development.

Verified versions during development: PHP 8.3.32, Composer 2.10.3, Node.js 22.23.1,
npm 10.9.8, Laravel 13.x, MySQL 8+ (tested against MariaDB 11.8.8).

---

## 1. Install the prerequisites

### Windows (main device)

Install each of the following, then **open a new terminal** so PATH updates apply:

1. **PHP 8.3+** — use one of:
   - [Laravel Herd](https://herd.laravel.com/windows) (recommended: bundles PHP + Composer), or
   - the [php.new](https://php.new/install/windows) installer (run in PowerShell):
     ```powershell
     Set-ExecutionPolicy Bypass -Scope Process -Force
     iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows'))
     ```
   Ensure `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `curl`, `fileinfo`, `zip` are enabled in `php.ini`.
2. **Composer** — [getcomposer.org/download](https://getcomposer.org/download/) (skip if using Herd, which includes it).
3. **Node.js 22+ (includes npm)** — [nodejs.org](https://nodejs.org).
4. **MySQL** — [MySQL Installer](https://dev.mysql.com/downloads/installer/) **or** [XAMPP](https://www.apachefriends.org/) (for MySQL only).
   Start the MySQL service / XAMPP MySQL before continuing.

Verify in a new terminal:
```powershell
php -v
composer -V
node -v
npm -v
mysql --version
```

### Linux

Debian/Ubuntu:
```bash
sudo apt update
sudo apt install -y php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath unzip git mariadb-server
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
# Node.js 22 via nvm (or your distro's package)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash
source ~/.bashrc && nvm install 22
sudo systemctl enable --now mariadb
```

Fedora/RHEL:
```bash
sudo dnf install -y php php-mysqlnd php-mbstring php-xml php-curl php-zip php-bcmath unzip git mariadb-server nodejs npm
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo systemctl enable --now mariadb
```

Verify:
```bash
php -v && composer -V && node -v && npm -v && mysql --version
```

---

## 2. Get the project

```bash
git clone https://github.com/Vallon-Doang/IhavetoomanygamesandIdontknowwhattopick.git
cd IhavetoomanygamesandIdontknowwhattopick
```

Install PHP dependencies and frontend dependencies:
```bash
composer install
npm ci
```

---

## 3. Configure the environment

Windows (PowerShell):
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Linux / macOS:
```bash
cp .env.example .env
php artisan key:generate
```

---

## 4. Create the database

Create an empty MySQL database named `gamespin`:

Windows (if `mysql` is on PATH):
```powershell
mysql -u root -p -e "CREATE DATABASE gamespin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Linux:
```bash
sudo mariadb -e "CREATE DATABASE gamespin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# or: sudo mysql -e "CREATE DATABASE ..."
```

Then edit `.env` with your connection details:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gamespin
DB_USERNAME=root
DB_PASSWORD=your-mysql-password
```

> Steam is optional. Leave `STEAM_API_KEY=` empty to use the seeded demo library.

---

## 5. Build the database and assets

```bash
php artisan migrate --seed
npm run build
```

`migrate --seed` creates the schema and inserts deterministic demo data. It is safe to
re-run without duplicating users or games. On Windows, run these in PowerShell from the
project directory (same commands).

If a previous attempt left a partial database, reset it:
```bash
php artisan migrate:fresh --seed
```

---

## 6. Run the application

```bash
php artisan serve
```

Open <http://127.0.0.1:8000>.

Optional: for live-reloading frontend assets during development, run `npm run dev` in a
second terminal instead of `npm run build`.

---

## 7. Log in and verify

| Role | Email | Password |
|---|---|---|
| Member | `member@gamespin.test` | `password` |
| Admin | `admin@gamespin.test` | `password` |

1. Visit the landing page and register a new account (created as a **member**).
2. Log in as the seeded member → dashboard and seeded library appear.
3. Search/exclude a game in **Library**, spin in **Roulette**, confirm it in **History**.
4. Log in as the seeded admin → **Admin → Users / Games / Syncs**.
5. Confirm a member visiting `/admin` is denied (HTTP 403).

These demo credentials are for local use only and must be changed/removed in a real deployment.

---

## 8. Troubleshooting

| Problem | Fix |
|---|---|
| `could not find driver` | Enable/install `pdo_mysql` (Windows: uncomment in `php.ini`; Linux: install `php-mysql`/`php-mysqlnd`). |
| SQLSTATE connection refused | Start the MySQL/MariaDB service and re-check `DB_*` in `.env`. |
| “Vite manifest not found” / no styles | Run `npm ci` then `npm run build`. |
| Migration errors after partial setup | Recreate the empty database, then `php artisan migrate:fresh --seed`. |
| Steam sync fails or empty library | Set a valid `STEAM_API_KEY` and make the Steam profile's game details public; otherwise use the demo library. |
| Permission errors on `storage/` | Ensure the web server can write to `storage/` and `bootstrap/cache/`. |

---

## 9. Optional: enable real Steam integration

Set server-side credentials in `.env` (never commit them):
```dotenv
STEAM_API_KEY=your-steam-web-api-key
STEAM_CALLBACK_URL="${APP_URL}/steam/callback"
```
Then a member can **Link Steam** from the dashboard and **Synchronize library**. Failures
are non-destructive: the existing library is preserved and the error is shown.
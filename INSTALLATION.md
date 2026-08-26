# Nilak Installation Guide

This guide is Linux-first and cross-platform. It covers local development (Linux, macOS, Windows) and production deployment (shared hosting/cPanel/Linux server).

## 1) What Is `artisan`?

`artisan` is Laravel's command-line entry point (CLI script) at project root.

Common commands:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
php artisan schedule:run
```

You do not execute `artisan` directly in most hosts. Use it via PHP CLI:

```bash
php artisan <command>
```

## 2) Platform Notes (Linux / macOS / Windows)

Core installation flow is the same on all platforms. Main differences are shell tools, package managers, and permissions.

1. Path separator: Linux/macOS use `/`, Windows uses `\`.
2. Permissions: Linux/macOS typically need `chmod/chown`; Windows usually handles this via filesystem ACLs.
3. Node install: Linux often uses apt/dnf/nvm, macOS often uses Homebrew/nvm, Windows uses installer or nvm-windows.
4. Laravel commands are the same everywhere: `php artisan ...`.

## 3) Prerequisites

Nilak requires:

1. PHP 8.2+
2. Composer 2+
3. MySQL 8+ or MariaDB 10.6+
4. Node.js 18+ and npm
5. Required PHP extensions for Laravel

## 4) Local Development (Linux / macOS / Windows)

### 4.1 Clone and Install

```bash
git clone https://github.com/bkashaf/nilak.git
cd nilak
composer install
cp .env.example .env
php artisan key:generate
```

Windows PowerShell copy alternative:

```powershell
Copy-Item .env.example .env
```

### 4.2 Database Setup

Set DB values in `.env`, then run:

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 4.3 Build Frontend Assets

```bash
npm install
npm run build
```

For active development:

```bash
npm run dev
```

### 4.4 Run App Locally

```bash
php artisan serve
```

Default URL is usually:

1. http://127.0.0.1:8000

## 5) Production Deployment Overview

For real hosting, target state is:

1. Document Root points to `public`
2. HTTPS enabled
3. `storage` and `bootstrap/cache` writable
4. Scheduler cron configured
5. Installer completed at `/install`

Installer URL:

1. https://your-domain.com/install

## 6) Shared Hosting and Composer: Do You Need It on Server?

Not always.

1. If host supports Composer/SSH: run `composer install --no-dev --optimize-autoloader` on server.
2. If host does not support Composer: install dependencies locally, then upload project with `vendor` included.

Recommended note for open-source users:

1. If Composer is unavailable on host, build locally and upload the `vendor` directory with project files.

## 7) cPanel Deployment Steps

### 7.1 Required cPanel Sections

1. Domains / Addon Domains / Subdomains
2. MySQL Databases or Database Wizard
3. File Manager
4. SSL/TLS Status
5. Cron Jobs
6. MultiPHP Manager

### 7.2 Create Database and User

1. Create database
2. Create user
3. Attach user to database
4. Grant ALL PRIVILEGES

Save:

1. `DB_HOST` (usually `localhost`)
2. `DB_PORT` (usually `3306`)
3. `DB_DATABASE`
4. `DB_USERNAME`
5. `DB_PASSWORD`

### 7.3 Upload Files

Option A: File Manager upload/extract archive.

Option B: Git clone in cPanel (if enabled).

Confirm key paths exist:

1. `public`
2. `storage`
3. `bootstrap/cache`
4. `artisan`

### 7.4 Set Document Root to `public`

Example:

1. Project path: `/home/username/nilak`
2. Document Root: `/home/username/nilak/public`

### 7.5 Enable SSL

1. Issue certificate (AutoSSL/Let's Encrypt)
2. Confirm HTTPS is active
3. Set `APP_URL` to HTTPS domain

### 7.6 Writable Permissions

Linux baseline:

```bash
find storage -type d -exec chmod 775 {} \;
find storage -type f -exec chmod 664 {} \;
chmod -R 775 bootstrap/cache
```

If owner/group mismatch exists, adjust with host-compatible `chown` (if allowed).

## 8) Run Web Installer

Open:

1. https://your-domain.com/install

Steps:

1. Requirements
2. Database
3. Store Settings
4. Summary
5. Final Run

Final run performs environment write, app key generation, migrations/seeding, cache clear, and installer lock.

## 9) Scheduler (Cron)

Add cron job (once per minute):

```bash
/usr/local/bin/php /home/USERNAME/PROJECT_PATH/artisan schedule:run >> /dev/null 2>&1
```

If PHP path differs, ask hosting support for exact CLI binary path.

## 10) APP_URL and Cache

Set:

```env
APP_URL=https://your-domain.com
```

Then clear cache:

```bash
php artisan optimize:clear
```

## 11) Post-Install Validation

1. `/`
2. `/shop`
3. `/cart`
4. `/checkout`
5. Admin login and dashboard
6. Page builder and image upload

## 12) Troubleshooting

### 500 Error

1. Check permissions for `storage` and `bootstrap/cache`
2. Check logs in `storage/logs`
3. Run `php artisan optimize:clear`

### Database Connection Error in Installer

1. Recheck DB host/port/user/pass
2. Confirm DB privileges
3. cPanel may prefix db/user names

### Assets Not Loading

1. Ensure `public/build` exists
2. Verify Document Root is `public`
3. Hard refresh browser

### `/install` Not Accessible After Install

1. Usually expected (installer lock is active)
2. Reinstall only after controlled reset of lock and database

## 13) Updating Existing Production

1. Backup files and DB
2. Deploy updated code
3. Run migrations
4. Clear caches
5. Smoke-test checkout and admin

## 14) Open-Source Documentation Policy

To keep docs non-platform-specific:

1. Keep Linux/macOS/Windows local flow in one shared section
2. Keep production section generic for cPanel and Linux servers
3. Add platform-specific notes only where behavior differs (permissions, paths, package managers)

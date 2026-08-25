# Nilak Installation Guide

This document provides a full, production-focused installation guide for deploying Nilak on real shared hosting with cPanel and MySQL.

## 1) Deployment Overview

Nilak is a Laravel application and requires:

1. PHP 8.2+
2. MySQL or MariaDB
3. Writable permissions for storage and bootstrap/cache
4. A domain or subdomain with Document Root pointing to public
5. SSL enabled for the real domain

The web installer entry point is:

1. https://your-domain.com/install

## 2) Required cPanel Sections

Before starting, make sure you can access these cPanel tools:

1. Domains or Addon Domains or Subdomains
2. MySQL Databases or MySQL Database Wizard
3. File Manager
4. SSL/TLS Status or Let's Encrypt
5. Cron Jobs
6. MultiPHP Manager

Quick prerequisites:

1. Set PHP to 8.2 or newer.
2. Ensure Laravel-required PHP extensions are enabled.
3. Make sure your account has enough disk space for vendor, build artifacts, cache, and logs.

## 3) Create Database and User in cPanel

### Recommended: MySQL Database Wizard

1. Open MySQL Database Wizard.
2. Create a new database.
3. Create a database user.
4. Attach the user to the database.
5. Grant ALL PRIVILEGES.

### Alternative: MySQL Databases

1. Create the database.
2. Create the user.
3. Use Add User To Database.
4. Grant ALL PRIVILEGES.

Save these values for installer step:

1. DB_HOST (usually localhost)
2. DB_PORT (usually 3306)
3. DB_DATABASE
4. DB_USERNAME
5. DB_PASSWORD

## 4) Upload Project Files

### Option A: File Manager

1. Open File Manager.
2. Go to your account home directory, for example /home/username.
3. Upload your project zip file.
4. Extract the archive.
5. Verify project structure exists completely.

### Option B: Git Version Control (if available)

1. Open Git Version Control in cPanel.
2. Clone the repository.
3. Checkout the target branch, usually main.

Confirm these paths exist:

1. public
2. storage
3. bootstrap/cache
4. artisan

## 5) Set Domain Document Root to public

This is the most important security and routing step.

1. Open Domains.
2. Select your domain or subdomain.
3. Click Manage or Edit.
4. Set Document Root to your project public folder.

Example:

1. Project path: /home/username/nilak
2. Document Root: /home/username/nilak/public

If Document Root is wrong:

1. Site may fail to boot correctly.
2. Internal project paths may become web-accessible.

## 6) Enable SSL on Real Domain

1. Open SSL/TLS Status.
2. Select your domain.
3. Run AutoSSL or Issue certificate.
4. Confirm site opens with https.

If cPanel provides a Force HTTPS option, enable it.

Target state:

1. Public site URL is HTTPS.
2. Installer URL is HTTPS.
3. APP_URL uses HTTPS.

## 7) Set Writable Permissions

In File Manager, use Change Permissions.

Recommended baseline:

1. Directories: 755
2. Files: 644

Critical writable paths:

1. storage
2. bootstrap/cache

If permission errors occur:

1. Temporarily test with 775 on required directories.
2. Avoid 777 except very short troubleshooting windows.

## 8) Run Web Installer

Open:

1. https://your-domain.com/install

Installer sequence:

1. Requirements
2. Database
3. Store Settings
4. Summary
5. Final Run

Database step requires the values from section 3.

Store Settings step requires:

1. Store name
2. Default locale
3. Timezone
4. Currency label
5. Optional logo

Final Run performs:

1. Environment writing
2. Application key generation
3. Migrations
4. Seeders
5. Initial admin creation
6. Cache clear
7. Installer lock

## 9) Configure Laravel Scheduler Cron Job

In cPanel:

1. Open Cron Jobs.
2. Choose Once Per Minute.
3. Add command below.

Command example:

```bash
/usr/local/bin/php /home/USERNAME/PROJECT_PATH/artisan schedule:run >> /dev/null 2>&1
```

Replace USERNAME and PROJECT_PATH with real values.

If PHP binary differs on your host, ask support for the exact path.

## 10) Keep APP_URL on Real HTTPS Domain

Use this pattern:

```env
APP_URL=https://your-domain.com
```

After changes, clear caches:

```bash
php artisan optimize:clear
```

Wrong APP_URL can cause:

1. Bad redirects
2. Wrong generated links
3. Callback/form URL mismatches

## 11) Post-Install Validation Checklist

Test these routes and flows:

1. Home /
2. Shop /shop
3. Cart /cart
4. Checkout /checkout
5. Admin login and dashboard
6. Pages management and page builder
7. Image upload from page builder

## 12) Troubleshooting FAQ

### 500 Internal Server Error

1. Verify storage and bootstrap/cache permissions.
2. Check logs in storage/logs.
3. Run optimize clear.

```bash
php artisan optimize:clear
```

### Installer Database Connection Fails

1. Recheck DB host and port.
2. Confirm user has ALL PRIVILEGES.
3. Remember cPanel prefixes database and username in many hosts.

### CSS/JS Assets Not Loading

1. Confirm public/build exists.
2. Recheck Document Root points to public.
3. Hard refresh browser with Ctrl+F5.

### HTTP/HTTPS Redirect Issues

1. Ensure APP_URL is HTTPS.
2. Ensure SSL certificate is active and valid.

### /install Not Accessible After Install

1. This is expected when installer lock is active.
2. For reinstall, reset lock and database carefully.

## 13) Updating an Existing Production Installation

1. Backup files and database.
2. Deploy updated code.
3. Run required migrations.
4. Clear caches.
5. Smoke-test critical pages and checkout flow.

## 14) Final Delivery Checklist

Before handing over to customer or evaluator:

1. Full install tested on real domain.
2. SSL enabled.
3. Scheduler cron active.
4. APP_URL correct.
5. Admin and checkout validated.
6. No unresolved critical errors in logs.

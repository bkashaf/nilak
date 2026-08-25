# Nilak Installation Guide

This guide covers both local installation and production installation on shared hosting (cPanel + MySQL).

## 1) Local Installation (Windows/Linux/macOS)

1. Clone repository.
2. Install PHP dependencies.
3. Configure environment.
4. Run migrations and seeders.
5. Build frontend assets.
6. Run application.

Commands:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm install
npm run build
php artisan serve
```

Open local URL and verify:

1. Home page loads.
2. Admin login works.
3. Product/cart/checkout pages are reachable.

## 2) Production Installation (Real Domain + cPanel)

### Pre-requisites

1. A real domain or subdomain.
2. PHP 8.2+ hosting.
3. MySQL database access.
4. SSL certificate (recommended before final setup).

### Step-by-step

1. Upload project files to hosting.
2. In cPanel create MySQL database and user.
3. Assign Full Privileges to the user.
4. Set domain Document Root to project public folder.
5. Ensure write permission for storage and bootstrap/cache.
6. Open https://your-domain.com/install.
7. Complete Requirements step.
8. Complete Database step and run DB connection test.
9. Complete Store Settings step.
10. Review Summary and run final installation.
11. Login to admin panel and verify storefront and checkout.

### Post-install checklist

1. Confirm installer lock is active.
2. Confirm APP_URL uses https.
3. Set cPanel cron for scheduler:

```bash
php artisan schedule:run
```

4. Keep backups for .env and database.

## 3) Common Troubleshooting

1. 500 error after deploy:
- Check storage and bootstrap/cache permissions.
- Run optimize clear from project root:

```bash
php artisan optimize:clear
```

2. Database connection failed in installer:
- Re-check DB host, port, db name, username, password.
- Verify user has Full Privileges.

3. Assets not loading:
- Run frontend build again:

```bash
npm run build
```

4. Wrong homepage URL or redirects:
- Verify APP_URL in .env matches domain and protocol.

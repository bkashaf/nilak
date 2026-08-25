# Nilak Commerce Platform

Nilak is an open-source Laravel-based commerce platform focused on modular architecture, Persian/English localization, and a practical path from local development to real production hosting.

The project is published on GitHub for public access. Contributions, issue reports, and improvement ideas are very welcome.

## Why Nilak

- Clean domain-oriented structure for order and payment flows
- Multi-method payment lifecycle with status history and safeguards
- Inventory reservation and commit/release consistency
- Localized user experience for RTL and LTR storefronts
- Admin tooling for products, categories, pages, payments, and sliders
- Guided installer flow for cPanel + MySQL environments

## Current Features

- Storefront: home, shop, product, cart, checkout, order tracking
- Authentication: web login/register, profile editing
- Checkout: profile/new address source, receipt-bank JSON instructions
- Payments: COD, receipt-based, gateway-ready architecture
- Admin panel: orders, payments, payment methods, pages, slider management
- Localization: Persian/English language switching
- Installer wizard: requirements, DB test, store settings, final one-click install run

## Architecture Snapshot

- Framework: Laravel 12, PHP 8.2
- API Auth: Laravel Sanctum
- Domain modules under app/Domain for Cart, Order, Payment, Inventory
- Service-layer components for menu/slider and lifecycle transitions
- Blade-based frontend with Vite build pipeline

## Project Structure

- app/Domain
- app/Http/Controllers
- app/Http/Middleware
- app/Models
- app/Support
- resources/views/themes
- resources/views/installer
- routes/web.php
- routes/admin.php
- config/payment.php
- config/installer.php

## Installation

For a full deployment-focused guide, see INSTALLATION.md.

### Local Development

1. Clone the repository.
2. Install dependencies.
3. Configure environment.
4. Run migrations and seeders.
5. Build frontend assets.

Example commands:

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

### cPanel + MySQL (Production Domain)

Use the installer wizard at /install.

Step-by-step flow:

1. Upload project files to your hosting account.
2. In cPanel, create MySQL database, user, and assign Full Privileges.
3. Set domain/subdomain Document Root to the public folder of this project.
4. Ensure writable permissions for storage and bootstrap/cache.
5. Enable SSL certificate for your real domain (recommended before final install).
6. Open https://your-domain.com/install.
7. Pass Requirements step (warnings are shown for SSL/DocumentRoot mismatch hints).
8. Fill database and initial admin user in Database step, then run connection test.
9. Complete Store Settings step (store name, locale, timezone, currency, logo).
10. Review Summary step and run final installation.
11. Login to admin panel and verify storefront, checkout, and payment methods.

After successful install:

1. Keep the installer locked (automatic lock file is created).
2. Verify APP_URL uses https on production.
3. Configure scheduled task in cPanel for php artisan schedule:run.

## Open Source and Community

This project is intentionally open and community-friendly.

You can help by:

- Reporting bugs and edge cases
- Opening feature requests
- Improving docs and translations
- Sending pull requests for fixes and enhancements

If you open an issue, please include:

- What you expected
- What happened instead
- Reproduction steps
- Environment details (PHP, DB, OS)

## Contributing

1. Fork the repository.
2. Create a feature branch.
3. Commit with clear messages.
4. Open a pull request with context and screenshots if UI-related.

## Versioning and Releases

Recommended release policy (SemVer):

- Stable format: MAJOR.MINOR.PATCH (example: 1.0.0)
- Pre-release format: MAJOR.MINOR.PATCH-label.N (example: 1.0.0-rc.1)

Suggested plan for current stage:

1. Publish a public test release as v0.9.0-rc.1 (or v0.1.0-beta.1 if you prefer earlier-stage signaling).
2. Fix feedback issues from test deployment and user acceptance.
3. Publish phase-one stable as v1.0.0.
4. Use PATCH for hotfixes, MINOR for backward-compatible features, MAJOR for breaking changes.

## Attribution and Maintainer Credit

Maintainer and original developer attribution is listed in AUTHORS.

Respect request:

- Please keep attribution to original developer(s) in project documentation and notices.
- If you redistribute or fork, keep license and copyright notices intact.

Note: legal permissions and obligations are governed by the project license terms.

## Security

If you discover a security-related issue, please report it responsibly through a private channel before public disclosure.

## License

This project is released under the GNU General Public License v3.0 (GPL-3.0-or-later).
See the LICENSE file for the full text.

# Deployment Checklist - Ferdinand Safaris

This checklist ensures a smooth and secure deployment of the Ferdinand Safaris platform to a production environment.

## 1. Server Prerequisites

- [ ] **PHP 8.2+** with extensions: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `session`, `tokenizer`, `xml`.
- [ ] **Node.js & NPM** (Latest LTS recommended).
- [ ] **Composer** (v2+).
- [ ] **MySQL 8.0+** or equivalent database.
- [ ] **Redis** (Highly recommended for Reverb and Queueing).
- [ ] **Web Server** (Nginx or Apache) configured with SSL (Let's Encrypt).

## 2. Environment Configuration (`.env`)

Ensure the following production values are set:

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` (Generated via `php artisan key:generate`)
- [ ] `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- [ ] `OPENAI_API_KEY`
- [ ] `STRIPE_KEY` & `STRIPE_SECRET`
- [ ] `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`
- [ ] `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET` (For Echo compatibility)

## 3. Deployment Steps

Run these commands in the project root:

- [ ] `git pull origin main`
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `npm install && npm run build`
- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --class=CmsSeeder --force`

## 4. Production Optimizations

- [ ] `php artisan optimize` (Caches config and routes)
- [ ] `php artisan view:cache`
- [ ] `php artisan event:cache`

## 5. Background Processes (Supervisor)

Configure Supervisor to manage the following processes:

- [ ] **Laravel Reverb:** `php artisan reverb:start`
- [ ] **Queue Worker:** `php artisan queue:work --tries=3 --timeout=90`

> [!IMPORTANT]
> Ensure the server firewall allows traffic on the Reverb port (default: 8080 or 443 if proxied).

## 6. Security & Maintenance

- [ ] **Permissions:** Ensure `storage` and `bootstrap/cache` are writable by the web server user.
- [ ] **SSL:** Verify HTTPS is active and forcing redirect.
- [ ] **Backups:** Set up automated database and file backups.
- [ ] **Monitoring:** Configure error tracking (e.g., Sentry) and server monitoring.

# Deployment (shared hosting with cPanel)

Not tested on a real host yet; these are the standard steps. Requirements: PHP 8.2+ with the extensions in `LOCAL_XAMPP.md`, MySQL, SSH or cPanel Terminal (strongly recommended), cron, SSL.

## 1. Build locally (the server needs no Node)
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```
`public/build` is git-ignored: upload it (or build on the server if Node exists).

## 2. Upload and structure
Best practice: the app lives **outside** the web root.
```
/home/USER/goaustriatours/        <- whole project (app, bootstrap, vendor, ...)
/home/USER/public_html/           <- contents of /public (index.php, build/, .htaccess)
```
Then edit `public_html/index.php` so the two paths point at `../goaustriatours/vendor/autoload.php` and `../goaustriatours/bootstrap/app.php`. (Alternative: point the domain's document root to `goaustriatours/public` in cPanel > Domains.) Create the storage link on the server: `php artisan storage:link` (if symlinks are blocked, copy `storage/app/public` to `public_html/storage` after each upload).

## 3. Database and `.env`
Create a MySQL database + user in cPanel. Create `.env` (never commit it):
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://goaustriatours.com
DB_HOST=localhost  DB_DATABASE=...  DB_USERNAME=...  DB_PASSWORD=...
SESSION_DRIVER=file  CACHE_STORE=file  QUEUE_CONNECTION=database
MAIL_MAILER=smtp  MAIL_HOST=...  MAIL_PORT=587  MAIL_USERNAME=...  MAIL_PASSWORD=...  MAIL_FROM_ADDRESS=hello@goaustriatours.com
RECAPTCHA_SITE_KEY=<real v2 checkbox key>   RECAPTCHA_SECRET_KEY=<real secret>
GHL_API_TOKEN=<PIT>   GHL_LOCATION_ID=<sub-account id>
ADMIN_SEED_EMAIL=you@example.com   ADMIN_SEED_PASSWORD=<strong, one-time>
```
Register the real domain for the reCAPTCHA keys in the Google reCAPTCHA console.

## 4. First release
```bash
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force          # first time only: admin + sample content. Then remove ADMIN_SEED_PASSWORD from .env
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
(`config:cache` means `.env` is only read at cache time: re-run it after any `.env` change.) Then log in at `/admin`, change the password, fill **Settings**, replace sample content (see `CONTENT_TO_CONFIRM.md`).

## 5. Queue worker (emails + GHL sync)
With `QUEUE_CONNECTION=database`, jobs wait until a worker runs. On shared hosting, use a cron every minute:
```
* * * * * cd /home/USER/goaustriatours && /usr/local/bin/php artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1
```
(Use your host's PHP 8.2 binary path.) On a VPS use Supervisor running `php artisan queue:work --tries=5 --sleep=3`. If you cannot run workers at all, set `QUEUE_CONNECTION=sync`: emails and CRM calls then run during the request (a slow CRM adds latency but never blocks saving the enquiry).

## 6. Scheduler
This project defines no scheduled tasks today. If you add some, add the standard cron: `* * * * * cd /home/USER/goaustriatours && php artisan schedule:run`.

## 7. Sitemap and robots
Both are dynamic routes (`/sitemap.xml`, `/robots.txt`): nothing to generate. Make sure there is **no** static `public/robots.txt`. Submit `https://goaustriatours.com/sitemap.xml` in Google Search Console.

## 8. Updating later
```
git pull (or upload) -> composer install --no-dev -o -> npm run build locally + upload public/build
php artisan migrate --force && php artisan config:cache route:cache view:cache && php artisan queue:restart
```

## 9. Hardening checklist
HTTPS forced (cPanel AutoSSL + redirect), `APP_DEBUG=false`, `.env` not web-accessible (it is outside `public_html`), `storage/` and `bootstrap/cache` writable, daily DB backups (enquiries contain consent evidence you may need for chargebacks), PHP 8.2+. Security headers are set in `SecurityHeaders` middleware; add a Content-Security-Policy once GTM/reCAPTCHA domains are final.

# Running locally on Windows + XAMPP

Verified on this machine: XAMPP **PHP 8.2.12** (`C:\xampp\php`), MySQL (MariaDB) from XAMPP, Node 26, Composer 2.11.

## 1. PHP requirements
Laravel 12 needs PHP 8.2+. Enable these in `C:\xampp\php\php.ini` (remove the leading `;`), then restart Apache if you use it:

```
extension=mbstring     extension=openssl    extension=pdo_mysql   extension=fileinfo
extension=curl         extension=gd         extension=intl        extension=zip
```
`gd` and `intl` were off by default and were **enabled on this machine** (a backup is at `php.ini.bak-goaustria`). Check with `C:\xampp\php\php.exe -m`.

`php` and `composer` are not on PATH. In a terminal either use full paths or, for the session:
```powershell
$env:Path = "C:\xampp\php;C:\Users\lpagu\bin;$env:Path"
```

## 2. Database
Start MySQL in the XAMPP control panel. Create the database (phpMyAdmin at <http://localhost/phpmyadmin>, or SQL):
```sql
CREATE DATABASE goaustriatours CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
`.env.example` already uses the XAMPP defaults: `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=goaustriatours`, `DB_USERNAME=root`, empty password. (To use a dedicated user: `CREATE USER 'gat'@'localhost' IDENTIFIED BY '...'; GRANT ALL ON goaustriatours.* TO 'gat'@'localhost';` and set it in `.env`.)

## 3. Install and build
```bash
composer install
npm install
copy .env.example .env          # then edit ADMIN_SEED_EMAIL / ADMIN_SEED_PASSWORD
php artisan key:generate
php artisan migrate --seed      # admin from .env, sample content, quiz, 16 images -> WebP (takes ~20s)
php artisan storage:link
npm run build                   # or: npm run dev  (live editing, keep it running)
```

## 4. View the site
**(a) Artisan server (simplest)**
```bash
php artisan serve               # http://127.0.0.1:8000
```
If it fails on Windows, use PHP's built-in server with Laravel's router script:
```bash
php -S 127.0.0.1:8000 -t public vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
```
(Laravel 12 has no `server.php` in the project root; the router script is inside the framework.)

**(b) Apache virtual host: `goaustriatours.test`** (not configured automatically, because it edits shared XAMPP/Windows files)

1. `C:\xampp\apache\conf\extra\httpd-vhosts.conf`: keep a `localhost` entry first so other projects keep working, then add:
```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "C:/xampp/htdocs"
</VirtualHost>

<VirtualHost *:80>
    ServerName goaustriatours.test
    DocumentRoot "D:/DG Ventures/code/goaustriatours/public"
    <Directory "D:/DG Ventures/code/goaustriatours/public">
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog "logs/goaustriatours-error.log"
</VirtualHost>
```
2. Confirm `httpd.conf` includes `Include conf/extra/httpd-vhosts.conf` and that `mod_rewrite`, `mod_headers`, `mod_deflate` are loaded.
3. Edit `C:\Windows\System32\drivers\etc\hosts` **as Administrator**, add: `127.0.0.1  goaustriatours.test`
4. Set `APP_URL=http://goaustriatours.test` in `.env`, run `php artisan config:clear`, restart Apache.

The `.htaccess` in `public/` (rewrite, gzip, long cache for hashed assets) was tested on XAMPP's Apache 2.4 + mod_php on a temporary port.

## 5. Local email
`MAIL_MAILER=log`: enquiry emails are written to `storage/logs/laravel.log` (search for `Subject: New enquiry`). Staff recipients come from **Admin > Settings > Enquiry notification emails** (none set = no email, enquiry still saved).
To see rendered emails instead, install [Mailpit](https://github.com/axllent/mailpit), run it, and set `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`; inbox at <http://localhost:8025>.

## 6. reCAPTCHA locally
`.env.example` ships Google's **public test keys** (site `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`, secret `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`). The checkbox always passes and shows a "for testing purposes only" banner. **Never use them in production**: set real keys in `.env` or Admin > Settings. The widget needs internet access (it loads from Google).

## 7. Queue
Locally `QUEUE_CONNECTION=sync`: emails and GHL sync run inside the request (after validation), so no worker is needed. In production see `DEPLOYMENT.md`.

## 8. Tests
`php artisan test`: uses in-memory SQLite and a blanked GHL config; it never touches your MySQL data or the live CRM.

## Troubleshooting
| Symptom | Fix |
|---|---|
| Port 80 in use (Apache won't start) | Skype/IIS/other service. `netstat -ano \| findstr :80`, stop it, or use `php artisan serve` instead. |
| `could not find driver` / missing extension | Enable the extension in `php.ini` (section 1), restart. Make sure you edit the php.ini XAMPP loads: `php --ini`. |
| `No application encryption key` | `php artisan key:generate` |
| `SQLSTATE[HY000] [2002]` | MySQL not running, or wrong `DB_HOST`/`DB_PORT`. |
| Images 404 / broken | `php artisan storage:link` (re-run if you moved the folder). |
| Styles missing / `Vite manifest not found` | `npm run build` (or leave `npm run dev` running). Delete `public/hot` if a dev server was killed. |
| **419 Page Expired** | Session/CSRF problem: clear cookies, `php artisan config:clear`, ensure `APP_URL` matches the address in the browser, and `storage/framework/sessions` is writable. |
| `Permission denied` writing `storage/` | Right-click `storage` and `bootstrap/cache` > Properties > make sure your user can write. |
| reCAPTCHA box never appears | No internet, or ad-blocker; check browser console. The script loads when you first click into a form. |
| `Class "..." not found` after pulling code | `composer dump-autoload` |

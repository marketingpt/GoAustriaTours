# GoAustriaTours: project map for Claude

> **Update this file whenever you change anything it describes.** It is a map, not a manual: details live in `docs/reference/`.

## What this is
A fast, server-rendered **Google Ads landing site** for GoAustriaTours (premium Austria tours, US travellers, USD). It captures enquiries through one shared form pipeline and syncs them to GoHighLevel (GHL). It is the starter skeleton for other GoTour brands: re-skin via `resources/css/theme.css` and settings, not by forking code.

## Stack
Laravel 12 (PHP 8.2), Blade (no SPA), Tailwind CSS v4 + Vite, Alpine.js (+focus plugin), Trix (admin rich text), Intervention Image (GD, WebP). MySQL (utf8mb4); SQLite **in-memory only for tests**. No Filament/WordPress/page builders.

## Folder map
```
app/Http/Controllers/SiteController.php      public pages (home, packages, destinations, about, contact, legal, thank-you)
app/Http/Controllers/EnquiryController.php   POST /enquiry: the ONLY endpoint behind every form
app/Http/Controllers/Admin/*                 /admin (CrudController = generic list/form base)
app/Http/Requests/EnquiryRequest.php         validation: consent, reCAPTCHA, honeypot, phone, sanitising
app/Services/EnquiryService.php              saves enquiry + consent evidence, emails staff, queues GHL sync
app/Services/Recaptcha.php                   server-side verify (fails closed)
app/Services/Ghl/                            GhlClient, GhlFields (field names!), GhlPayloadBuilder
app/Services/ImageService.php                upload -> WebP 480/800/1200/1600
app/Support/{Consent,Tracking,Seo,Html,Phone}.php, helpers.php (setting(), ghl_enabled())
app/Jobs/SyncEnquiryToGhl.php, app/Mail/NewEnquiryMail.php, app/Console/Commands/Ghl{Setup,Test}.php
config/{site,home,countries,services}.php   settings schema, editable home copy, dial codes, keys
database/seeders/                            idempotent seed (admin from .env, sample content, quiz)
database/seed-images/                        Pexels photos + manifest.json (credits)
resources/css/{theme,base,app,admin}.css     TOKENS live in theme.css
resources/js/{app,admin,forms,recaptcha}.js
resources/views/{layouts,partials,site,admin,components,emails,errors}
routes/{web,admin}.php
docs/reference/*.md                          all other documentation
```

## Run on XAMPP (details: docs/reference/LOCAL_XAMPP.md)
```
composer install && npm install && npm run build
php artisan key:generate && php artisan migrate --seed && php artisan storage:link
php artisan serve          # http://127.0.0.1:8000   (admin: /admin)
```
PHP lives at `C:\xampp\php`, Composer at `C:\Users\lpagu\bin\composer.bat` (not on PATH by default). XAMPP MySQL must be running; DB `goaustriatours`, user `root`, no password.

## Business rules that are NOT obvious from the code
- **Consent text is compliance text.** Exact wording lives only in `App\Support\Consent`; company name comes from setting `legal_name`. Never reword. Stored verbatim on each enquiry with version (`terms_version`), timestamp, IP, user agent, page URL.
- **Every form** (hero quiz, header/package modal, package "Get a Quote", "Ask a question", contact) uses `<x-enquiry-form>` -> `POST /enquiry` -> `EnquiryRequest` -> `EnquiryService`. Do not build a second path.
- A form cannot submit without the **unticked-by-default consent checkbox** and a **passed reCAPTCHA v2** (verified server-side; fails closed). Honeypot field `company_website` must stay empty. Rate limit 5/min and 20/hour per IP.
- **No-JS fallback:** the quiz card collapses to the plain contact form (`.no-js .js-only`). Keep `keep-nojs` on the quiz contact wrapper.
- **Tracking:** first touch (30-day cookie `gat_first`) and last touch (`gat_last`) are captured on every public GET by `CaptureTracking`; stored on the enquiry as JSON and sent to GHL as `<key>_first` / `<key>_last`.
- **GHL is OFF** when `GHL_API_TOKEN` or `GHL_LOCATION_ID` is empty (`ghl_enabled()`); never blocks an enquiry; token only in `.env`. Field names are defined ONLY in `GhlFields`; rename in GHL and there together. Quiz step `label` becomes the GHL field `Quiz - <label>`.
- **Quiz is data-driven** (`quiz_steps`, `quiz_options`); answers stored as JSON on the enquiry. `field_key` is permanent.
- **Sample content is flagged** (`packages.is_sample`, `testimonials.is_placeholder`) and sample prices are never emitted as structured data. See `docs/reference/CONTENT_TO_CONFIRM.md`. Never invent business facts.
- Rich text from the admin is passed through `App\Support\Html::clean()` (allow-list). Legal pages use the `{company}` token.
- Images: free-licence only (Pexels/Unsplash/Pixabay/own); log each in `docs/reference/IMAGE_CREDITS.md`.

## Conventions
- Blade components in `resources/views/components` (`button`, `section-heading`, `card`, `form-field`, `faq-item`, `picture`, `icon`, `package-card`, `quiz-card`, `enquiry-form`, `admin/*`). Use design tokens (`brand-*`, `gold-*`, `cta-*`), never raw hex.
- Each page passes `$seo = Seo::make(...)` to `layouts.site`; one `<h1>` per page.
- Simple admin lists extend `Admin\CrudController`; add the route in `routes/admin.php` via `$crud(...)`.
- Format with `vendor/bin/pint`. Tests: `php artisan test`.

## Gotchas
- **Tests use in-memory SQLite and force-blank GHL** (`phpunit.xml`, with `force="true"`). Never remove that: `RefreshDatabase` would wipe the dev MySQL DB (this happened once).
- `public/robots.txt` must not exist: robots.txt is a route (`SeoController`).
- Cookies in tests: `postJson` drops cookies unless `->withCredentials()`.
- Media URLs use `asset('storage/...')`, so run `php artisan storage:link`.
- `php artisan serve` is single-threaded; Lighthouse on it is pessimistic and the simulated-throttling run of the home page can fail with NO_NAVSTART (use `--throttling-method=devtools`).
- Admin Settings keys (`site.fields`) override `config/site.php` defaults; `setting()` falls back to defaults.

# GoAustriaTours

Server-rendered Google Ads landing site (Laravel 12, Tailwind v4, Alpine) with a hero quiz, enquiry capture, GoHighLevel sync and a Blade admin at `/admin`.

## Get running (Windows + XAMPP)

Start **MySQL** in the XAMPP control panel, then:

```bash
composer install && npm install && npm run build
copy .env.example .env && php artisan key:generate   # skip the copy if .env already exists
php artisan migrate --seed && php artisan storage:link && php artisan serve
```

Open <http://127.0.0.1:8000>. Admin: <http://127.0.0.1:8000/admin>, login = `ADMIN_SEED_EMAIL` / `ADMIN_SEED_PASSWORD` from `.env` (set these before seeding).

Live asset editing: `npm run dev` in a second terminal. Tests: `php artisan test`.

## PlanMyTour package sync

The site can mirror the public PMT package feed for company 3. The sync is intentionally read-only: it fetches the current feed, upserts packages by PMT id, deletes locally orphaned PMT packages, and refreshes local TourType names from the feed’s lookup table.

- Read-only feed: `https://api.planmytour.app/api/v2/public/packages/3`
- Manual run: `php artisan pmt:sync-packages --company-id=3`
- Dry run: `php artisan pmt:sync-packages --company-id=3 --dry-run`
- Scheduled run: set `PMT_SYNC_ENABLED=true` and the scheduler will execute every 10 minutes.
- Safety rules: 200 + `response: true` only; ETag support via `If-None-Match`; no deletes when more than half of current PMT-sourced packages would be removed in one run; non-JSON/5xx/timeout = no changes.

## Documentation

See [CLAUDE.md](CLAUDE.md) for the project map and rules, and `docs/reference/`:
[LOCAL_XAMPP](docs/reference/LOCAL_XAMPP.md) · [DEPLOYMENT](docs/reference/DEPLOYMENT.md) · [ADMIN_GUIDE](docs/reference/ADMIN_GUIDE.md) · [FORMS_AND_TRACKING](docs/reference/FORMS_AND_TRACKING.md) · [GHL_INTEGRATION](docs/reference/GHL_INTEGRATION.md) · [SEO_CHECKLIST](docs/reference/SEO_CHECKLIST.md) · [DESIGN_SYSTEM](docs/reference/DESIGN_SYSTEM.md) · [IMAGE_CREDITS](docs/reference/IMAGE_CREDITS.md) · [CONTENT_TO_CONFIRM](docs/reference/CONTENT_TO_CONFIRM.md)

> Terms and Privacy pages are **starter templates, not legal advice**. Have a lawyer review them before launch.

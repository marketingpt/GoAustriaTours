# SEO and performance checklist

## Built in
- Server-rendered HTML on every page (no SPA), one `<h1>`, semantic landmarks (`header/nav/main/footer`), skip link, breadcrumb on inner pages. A test asserts one H1 + title + description + canonical on every public page.
- Unique `<title>`, meta description, canonical, Open Graph and Twitter cards per page (edit in Admin: Packages, Destinations, Pages; defaults in Settings).
- JSON-LD: `TravelAgency` (all pages), `BreadcrumbList`, `FAQPage` (home + package FAQs), `TouristTrip` (packages). **Sample prices are never emitted** as `Offer` data.
- `sitemap.xml` (dynamic; excludes unpublished and noindex), `robots.txt` (blocks `/admin`, `/thank-you`, `/enquiry`), per-page **noindex** toggle, Thank You page is noindex.
- Images: WebP in 4 sizes with `srcset`, width/height attributes (no layout shift), lazy-loaded below the fold, hero image preloaded + `fetchpriority=high`, alt text required at upload.
- Fonts self-hosted (variable WOFF2), preloaded, `font-display: swap` with **metric-matched fallbacks** (no layout shift on swap).
- JavaScript: Alpine only (~30 KB gz total JS on public pages); reCAPTCHA is fetched lazily on first form interaction. GTM only if configured.
- `.htaccess`: gzip, 1-year immutable caching for hashed build assets and uploaded images.
- Mobile first; tested at 360 / 390 / 768 / 1280 widths with no horizontal scroll; `prefers-reduced-motion` respected; focus outlines; contrast checked by Lighthouse.

## Lighthouse results (mobile, run 2026-11-10 on XAMPP Apache with gzip + cache headers)
| Page | Performance | Accessibility | Best practices | SEO | LCP | CLS | TBT |
|---|---|---|---|---|---|---|---|
| `/` | 90 | 100 | 100 | 100 | 3.5 s | 0 | 110 ms |
| `/packages` | 100 | 100 | 100 | 100 | 0.8 s | 0 | 40 ms |
| `/packages/{slug}` | **79** (DevTools throttling) | 100 | 100 | 100 | 3.1 s | 0.001 | 450 ms |
| `/destinations/vienna` | 96 | 100 | 100 | 100 | 2.8 s | 0 | 60 ms |
| `/contact` | 100 | 100 | 100 | 100 | 0.8 s | 0 | 80 ms |
| `/about` | 97 (DevTools throttling) | 100 | 100 | 100 | 1.9 s | 0 | 140 ms |

Caveats, stated plainly: (1) the default "simulated" run failed with a Lighthouse `NO_NAVSTART` tracing error on some pages (reliably on package detail and sometimes on `/about`), so those use the stricter real-network DevTools throttling mode and are not directly comparable. An earlier simulated run of the package page scored 92. (2) Numbers are from a local machine; re-run against the live URL (PageSpeed Insights) after deploying. (3) **The package detail page is below the 90 target** under DevTools throttling (main-thread work from the form, gallery and Alpine). Ideas: lazy-initialise the sidebar form's country select, shrink the gallery thumbnails, defer Alpine start to idle.

Re-run: `npx lighthouse https://yoursite/ --only-categories=performance,accessibility,best-practices,seo` (add `--throttling-method=devtools` if you hit NO_NAVSTART).

## Pre-launch checks
- [ ] Domain live over HTTPS, www/non-www redirect decided, `APP_URL` correct, `APP_DEBUG=false`.
- [ ] Every `[CONFIRM]` item in `CONTENT_TO_CONFIRM.md` resolved; sample packages/testimonials replaced or marked.
- [ ] Settings filled: brand + legal company name, phone, email, address, notification emails, default SEO text + share image, favicon/logo.
- [ ] Real reCAPTCHA keys for the production domain (not the test keys); submit each form once on the live site.
- [ ] GTM container ID set; Ads conversion fires on `/thank-you` only (GTM Preview); gclid captured (test `?gclid=TEST`).
- [ ] GHL: PIT + location in `.env`, `php artisan ghl:setup` then `ghl:test` pass; a live test enquiry appears with UTM fields.
- [ ] Terms/Privacy reviewed by a lawyer; SMS wording approved; cookie/consent banner if serving EU/UK.
- [ ] Unique title (<= 60 chars) and description (<= 155) on Home, each package, each destination, About, Contact.
- [ ] `sitemap.xml` loads; submit it in Google Search Console and Bing; `robots.txt` has no static override.
- [ ] No page unintentionally `noindex`; Thank You is `noindex`.
- [ ] Rich Results Test passes for a package (TouristTrip) and the home page (FAQPage).
- [ ] Lighthouse mobile >= 90 on the live Home and a package page; check Core Web Vitals in Search Console after a few weeks.
- [ ] Ads landing page: headline matches the ad, one clear CTA, phone number is tap-to-call, 404 page works.
- [ ] Backups scheduled; queue worker/cron running (send a test enquiry and confirm the email + CRM contact).

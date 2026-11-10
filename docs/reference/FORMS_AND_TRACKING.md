# Forms, consent and tracking

## One pipeline for every form
`<x-enquiry-form>` (Blade) -> `POST /enquiry` -> `EnquiryRequest` -> `EnquiryService`. Used by: the hero quiz (final step), the header/top-bar/package/tour-type pop-up modal (one shared modal, opened with the `open-enquiry` browser event), the package-page "Get a quote" form, "Ask us a question", and the Contact page. The form name is stored with the enquiry (`Hero Quiz`, `Header Modal`, `Package Quote`, `Package Question`, `Tour Type Quote`, `Tour Type Question`, `Contact Page`, ...).

Fields: first/last name*, email*, country code + phone*, preferred travel date (approx.), second quote from a partner (yes/no), special request; extended forms add time of year, group size, nights. Package forms also send `package_id` (+ name; the server trusts only the id).

## Protections (all enforced on the server, not only in the browser)
| Control | Where |
|---|---|
| CSRF token | `@csrf`, Laravel middleware (419 if missing) |
| Required, **unticked** consent checkbox | `consent => accepted` in `EnquiryRequest`; browser `required` |
| reCAPTCHA v2 checkbox, verified with Google | `App\Services\Recaptcha`; **fails closed** (no token, wrong token, Google down = rejected) |
| Honeypot `company_website` | must be empty |
| Rate limit | 5/minute and 20/hour per IP (`enquiries` limiter) |
| Sanitising | tags stripped, lengths capped, country must be in `config/countries.php`, quiz answers must match an active option |
| Phone | stored as E.164 (`+12125550123`) from the country select + number (`App\Support\Phone`) |
| Inline errors, labels, focus styles | `form-field` component, Alpine `errors` for AJAX and server errors for no-JS |

reCAPTCHA uses **explicit rendering** so several widgets can share a page (inline form, quiz, modal). The Google script is fetched lazily on the first interaction with a form. A `<noscript>` reCAPTCHA fallback is included for no-JS users.

## Consent text (compliance, do not edit casually)
`App\Support\Consent::text()` builds this, with the legal company name from Settings:

> I agree to the Terms and Conditions and Privacy Policy provided by [Company Name]. By providing my phone number, I consent to receive SMS messages from [Company Name] regarding my enquiry, booking updates, itinerary updates, reminders, and customer support. Message and data rates may apply. Reply STOP to unsubscribe or HELP for assistance. Consent is not a condition of purchase.

On screen, "Terms and Conditions" and "Privacy Policy" link to those pages in a new tab. Each enquiry stores: `consent_given`, `consent_text` (the exact string shown), `terms_version`, `consent_at`, `ip_address`, `user_agent`, `page_url` (the page the form was on). Visible in Admin > Enquiries > Open, and in the CSV export. Bump **Settings > Terms version** when legal text changes.

## Success and thank-you
JS: the form shows a success message, then redirects to `/thank-you` (noindex). No JS: normal POST + redirect. The Thank You page pushes the conversion event once per enquiry (a one-time session flag), so reloads do not double count.

## Attribution capture
`CaptureTracking` middleware (all public GET requests):
- `gat_first`: set once, kept **30 days**: utm_source, utm_medium, utm_campaign, utm_term, utm_content, gclid, gbraid, wbraid, referrer (external only), landing_page, captured_at.
- `gat_last`: refreshed whenever a visit carries campaign parameters (falls back to first touch).
Both are saved with the enquiry (`tracking_first`, `tracking_last`; `source` = first-touch utm_source, `google-ads` if only a gclid, else `direct`) and sent to GHL. Mention the cookies in the Privacy Policy and in any cookie banner you add.

## Google Tag Manager and Google Ads conversion
1. Admin > Settings > **Google Tag Manager ID** (`GTM-XXXXXXX`). The snippet (head + noscript body) is only output when set.
2. The page always initialises `window.dataLayer`. Events pushed:
   - `enquiry_form_submit` (`form_name`): when a visitor submits a form (before the server answers; an *attempt*, may still fail validation).
   - `generate_lead` (`form_name`, `enquiry_id`): on `/thank-you`, once per saved enquiry. **Attach your Google Ads conversion to this one.**
3. In GTM: Triggers > New > Custom Event, event name `generate_lead`. Tags > New > *Google Ads Conversion Tracking* (conversion ID + label from Google Ads) with that trigger. Optionally add a GA4 event tag `generate_lead`. Also add the *Conversion Linker* tag on All Pages (so gclid is stored).
4. Test in GTM Preview mode: submit a form with the test keys and confirm the tag fires on `/thank-you` only.
5. Consent: if you serve EU/UK visitors you will need a consent banner/Consent Mode. Not included.

## Mail
Staff notification (`NewEnquiryMail`) is queued to the addresses in Settings. Locally it is written to the log; see `LOCAL_XAMPP.md`.

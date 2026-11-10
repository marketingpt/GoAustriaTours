# GoHighLevel (GHL) integration

Status: **built and unit-tested against mocked responses. Not yet run against a real GHL account** (no PIT was available). Run `ghl:setup` and `ghl:test` first when you have credentials and report any API differences.

## What it does
After an enquiry is saved, `SyncEnquiryToGhl` (queued) upserts the person as a GHL contact via API v2 (`https://services.leadconnectorhq.com`, header `Version: 2021-07-28`):
1. loads custom-field IDs (cached 1 hour),
2. looks the contact up by email (`GET /contacts/search/duplicate`),
3. `POST /contacts/upsert` with name, email, phone (E.164), tags and custom fields,
4. records `ghl_status` (`pending|synced|failed|disabled`), `ghl_contact_id`, `ghl_error` on the enquiry.

**Off switch:** if `GHL_API_TOKEN` or `GHL_LOCATION_ID` is empty nothing is sent (`ghl_status = disabled`). Failures are logged (token scrubbed), retried with backoff (30s, 2m, 10m, 30m; 5 tries) on a real queue, and **never block or fail the enquiry**. Retry manually in Admin > Enquiries > Open > *Retry sync*. Tests force-blank both values (`phpunit.xml`).

## Get the credentials
1. In GHL open the **sub-account** > *Settings* > *Private Integrations* > **Create new integration**.
2. Name it e.g. "GoAustriaTours website". **Scopes** (tick these):
   - `contacts.readonly`, `contacts.write` (look up, upsert, and delete the test contact)
   - `locations/customFields.readonly`, `locations/customFields.write` (list + create fields)
3. Copy the token (shown once). Sub-account/Location ID: *Settings* > *Business Profile* (or the ID in the dashboard URL).
4. Put both in `.env` only: `GHL_API_TOKEN=...` and `GHL_LOCATION_ID=...`, then `php artisan config:clear` (or `config:cache` on production). Never commit them.

## Commands
```bash
php artisan ghl:setup            # create every missing custom field (safe to re-run; --dry-run lists only)
php artisan ghl:test             # verify token+location, upsert a labelled TEST contact, then delete it (--keep to leave it)
```
`ghl:setup` lists existing fields first, skips any whose **exact name** exists, creates the rest, prints what it did, and refreshes the cache. Re-run it after adding a quiz question.

## Custom fields (names defined ONLY in `App\Services\Ghl\GhlFields`)
Match is by **exact name**. Staff may move fields into folders in GHL (the API cannot create folders) but **must not rename them**. To rename: change it in GHL and in `GhlFields` together.

| Group | Fields |
|---|---|
| Enquiry | `Preferred Travel Date`, `Second Quote Preference`, `Special Request` (large text), `Package`, `Form Name` |
| Quiz | `Quiz - <label>` for every quiz step (defaults: Time of Year, Travel Style, Group Size, Nights, Flights Booked, Budget, Likelihood) and `Quiz - All Answers` (large text) |
| Consent | `Consent Given` (Yes), `Consent Timestamp` (ISO 8601), `Consent Text Version` |
| Tracking, first touch | `utm_source_first`, `utm_medium_first`, `utm_campaign_first`, `utm_term_first`, `utm_content_first`, `gclid_first`, `gbraid_first`, `wbraid_first`, `referrer_first`, `landing_page_first` |
| Tracking, latest touch | the same ten with `_last` |

Tags added: brand name, `source: <utm_source>`, `package: <name>` (if any), `form: <form name>`.

## UTM and ad tracking from every form
Hero quiz, header modal, package quotes, questions and Contact all go through the same code, so all send the full first-touch and latest-touch sets plus the form name. A test (`GhlTest::test_payload_includes_utm_and_tracking_fields_for_every_form_type`) asserts this for each form type.

## Returning contacts
`*_last` fields are updated on every submission. `*_first` fields are **only sent when the existing contact has no value** for them, so original attribution is never erased.

## Caveats to check on first live run
- Upsert de-duplication follows the sub-account's *duplicate contact* setting (email and/or phone).
- Field creation payload (`name`, `dataType` = `TEXT`/`LARGE_TEXT`, `model` = `contact`) follows the v2 docs; if GHL rejects a type, adjust in `GhlFields::all()`.
- Rate limits: one sync = 3 calls (+ field list when the cache is cold).

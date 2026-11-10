# Content to confirm before launch

Nothing on the site states invented facts (no statistics, awards, review counts, ratings, years in business, licence numbers or partner names). Everything below is a placeholder or generic wording that **needs your decision**. Items shown as `[CONFIRM: ...]` appear literally in the content until replaced.

## Business details (Admin > Settings)
- [ ] Phone number (tap-to-call hidden until set), public email, address
- [ ] Notification email(s) for new enquiries (none set = no staff email)
- [ ] Legal company name (currently "GoAustriaTours"; used in the consent text and legal pages), company registration details
- [ ] Logo, favicon, social share image, social links
- [ ] Google Tag Manager ID, Google Ads conversion (in GTM), real reCAPTCHA keys (test keys are in `.env.example`)
- [ ] GoHighLevel PIT + location ID (see `GHL_INTEGRATION.md`)

## Home page wording (Admin > Pages > Home) to validate as true for the business
- [ ] Trust badges: "Free, no-obligation quote", "Tailor-made itineraries", "Dedicated trip specialist". You mentioned *Best Price Guarantee*, *Local Experts* and *Reviews*; these were **not** used because they are claims that need proof. Add them only if true, with terms.
- [ ] "Austria know-how", "One person from start to finish" and similar "why us" points describe how you work; edit to match reality.
- [ ] Hero sub-heading says journeys are "planned by specialists".
- [ ] Modal heading: "Get your free personalised quote" (no discount % was set; add a real offer only if you have one).

## Packages (all 6 are SAMPLE data, flagged "Sample")
- [ ] Names, itineraries, highlights, inclusions/exclusions, nights and **"from" prices** (sample prices: $2,450; $2,150; $4,950; $4,450; $2,990; $2,250), price notes (`[CONFIRM]`)
- [ ] Real package images (current ones are generic Pexels photos)
- [ ] Untick "Sample" once confirmed

## Testimonials
- [ ] 3 placeholders ("Guest name, City, State"). Replace with real, permitted reviews. Do not publish counts or star ratings you cannot verify.

## FAQs (generic answers; check each)
- [ ] Lead times (`[CONFIRM]`), what the price includes (`[CONFIRM]`), how to pay (`[CONFIRM]`)
- [ ] Visa/entry wording (kept generic: "check official sources")
- [ ] Best-time-to-visit answer (general seasonal advice)

## Pages
- [ ] **About**: company story, team, years in business, accreditations (`[CONFIRM]`)
- [ ] **Terms and Conditions** and **Privacy Policy**: *starter templates, not legal advice.* Complete every `[CONFIRM]` (company details, quote validity, deposits, cancellation, insurance, liability, governing law, processors, retention, data-subject requests, SMS rules) and have a lawyer review, including US SMS (TCPA/CTIA) compliance. Then bump **Settings > Terms version**.
- [ ] Cookie/consent banner decision (EU/UK visitors, Consent Mode)

## Destinations
- [ ] Short descriptions are general knowledge (e.g. UNESCO listings); sanity-check wording and add your own expertise.

## SEO
- [ ] Final titles/meta descriptions per page; Search Console and Bing verification.

# Admin guide (plain English)

Log in at **yoursite.com/admin** with the email and password you were given. Use "Log out" (bottom of the left menu) when finished. Five wrong attempts in a minute locks the login for a short time.

The left menu has everything. On a phone, tap the menu icon at the top.

## Enquiries (your inbox)
Every form on the site lands here, newest first. New ones are highlighted.
- **Search/filter** by name, email, phone, status, source (where they came from, e.g. `google`), form, and dates. **Export CSV** downloads what you are looking at.
- **Open** an enquiry to see: their details, their quiz answers, the **consent record** (the exact wording they saw, date/time, IP, page), tracking info (which ad brought them), and CRM status.
- Set the **status**: New, Contacted, Quoted, Won, Lost, and add **internal notes** (never shown to customers). Click Save.
- **GoHighLevel**: "Synced" means the contact is in your CRM. If it says "Failed" or "Disabled", you can press **Retry sync** (once the CRM details are set up). The enquiry itself is always saved.
- **Delete** removes the enquiry and its consent record for good: only do this for genuine data-removal requests.

## Packages
**Add package** to create a journey. Fill in title, tour type, destination, nights and "from" price (leave the price empty to show "price on request"). Then:
- *Short summary* appears on cards. *Full description* is the rich-text overview.
- *Highlights, Included, Not included*: one item per line.
- *Day-by-day*: click **+ Add day**; use the arrows to reorder.
- *Images*: choose a main image and gallery images from the Media library (upload there first). Gallery order = the order you pick them; use the arrows.
- *SEO title / meta description / share image* control how it looks in Google and when shared.
- Tick **Published** to show it. **Featured** puts it on the home page (the first 3 featured are shown). **Sample** adds a "Sample" tag: untick it once the content is real.
In the list: **Duplicate** (copies as an unpublished draft), **Publish/Unpublish**, **Feature**, and the up/down arrows to reorder.

## Tour types and Destinations
The lists packages use. Tour types appear as cards on the home page and in the menu. Destinations get their own page (good for Google) with their own text and photo. Turning one off hides it from the site; deleting leaves packages in place with the field blank.

## Quiz
The multi-step box in the home page header. Each **question** has a short label, an answer style (Tiles: click moves on; Dropdown: needs Next), and a list of **options** (text, optional icon, active on/off, order). The contact form is always the last step. Order questions with the arrows. Turn a question off instead of deleting if you may want it back.
Important: the *label* becomes a field name in GoHighLevel ("Quiz - Time of Year"). If you rename a label after launch, rename that field in GoHighLevel too, or the answer will stop syncing. The internal *answer key* cannot be changed.

## Pages
Edit wording for Home (grouped by section), About, Contact, Terms, Privacy, plus SEO fields for each. **Terms and Privacy are starter templates** and must be reviewed by a lawyer; `{company}` is replaced by the legal company name from Settings. "Hide from search engines" (noindex) removes a page from Google.
**Testimonials** and **FAQs** have their own menu items. Mark a testimonial "placeholder" until it is a real, permitted review; only add real ones. FAQs also feed Google's FAQ results.

## Media
Upload JPG/PNG/WebP up to 8 MB (at least 600 px wide). **Alt text is required**: describe the picture in plain words (helps blind visitors and search engines). Images are resized and converted to WebP automatically. Only upload images you own or that are free for commercial use, and note the credit. Deleting an image removes it everywhere it is used.

## Settings
Brand name and **legal company name** (used in the consent wording and legal pages), logo, favicon, phone, email, address, social links, **enquiry notification emails** (comma-separated; who gets emailed for each enquiry), enquiry pop-up heading, default SEO text and share image, **Google Tag Manager ID** (like GTM-XXXXXXX; leave empty to switch tracking off), reCAPTCHA keys, and the **terms version** (change it, e.g. to `2026-06`, whenever you change Terms or Privacy so each consent record shows which version was agreed to).
The GoHighLevel box is read-only: the connection details are kept in the server's `.env` file by your developer.

## Admin users
Add people who can log in; remove anyone who should not. You cannot delete yourself or the last admin. Passwords need at least 10 characters. Leave the password blank when editing to keep the current one.

## Good habits
Reply to new enquiries quickly; keep statuses up to date; never put customer details in notes you would not want a customer to read; keep the Settings > legal company name correct.

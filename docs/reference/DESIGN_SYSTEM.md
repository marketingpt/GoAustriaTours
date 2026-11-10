# Design system and re-skinning

Look: premium, editorial, Alpine. Deep forest green + brass gold on warm ivory, a high-contrast serif for headings with italic accents, clean sans for text, generous spacing, one orange accent for calls to action. (Direction taken from the owner's reference site's structure and tone; its exact colours could not be read, so these are proposed values.)

## Tokens: one file
`resources/css/theme.css` (`@theme` block). Change these and the whole site (and admin) follows.

| Token | Use |
|---|---|
| `--color-brand-50..900` | Alpine green: headings (800), dark sections (700-900), links |
| `--color-gold-50..800` | Eyebrows, rules, accents (use 600+ for text on ivory) |
| `--color-cta-500..700` | Buttons and the quiz border (white text passes AA on 600/700) |
| `--color-ivory`, `--color-sand` | Page background, soft section background |
| `--color-ink`, `--color-ink-soft` | Body text |
| `--font-display`, `--font-sans` | Cormorant Garamond / Inter (self-hosted in `resources/fonts`) |
| `--radius-card`, `--shadow-card`, `--shadow-lift` | Shape and depth |

Utilities in `base.css`: `container-x` (page width + gutters), `section-y` (vertical rhythm), `eyebrow`, `prose-content` (rich text), `.input` (form controls).

## Components (`resources/views/components`)
| Component | Notes |
|---|---|
| `<x-button>` | `variant`: primary, secondary, outline, outline-light, light, ghost; `size`: sm, md, lg; `href` makes it a link; `icon` |
| `<x-section-heading>` | `eyebrow`, `title`, `text`, `align`, `light` (for dark sections) |
| `<x-card>` | white rounded card |
| `<x-faq-item>` | native `<details>` (works without JS) |
| `<x-form-field>` | label + control + hint + inline error (server and Alpine) |
| `<x-enquiry-form>` | THE form (see `FORMS_AND_TRACKING.md`) |
| `<x-quiz-card>` | hero multi-step quiz, data-driven |
| `<x-package-card>` | listing card with quote button (`heading` prop for correct heading level) |
| `<x-picture>` | responsive WebP with dimensions, lazy/eager, labelled placeholder when no image |
| `<x-icon>` | inline SVG set; names also used in the quiz icon picker |
| `partials/enquiry-modal` | single site-wide modal; open with `$dispatch('open-enquiry', {...})` |
| `<x-admin.*>` | admin inputs, rich text (Trix), media picker |

## Re-skinning for a new brand (checklist)
1. `theme.css`: replace the palette (keep the 50-900 scale names) and fonts; drop new WOFF2 files in `resources/fonts` and update the `@font-face` + preload paths in `layouts/site.blade.php`; re-measure the fallback `size-adjust` values.
2. Admin > Settings: brand name, legal company name, logo, favicon, contacts, GTM, keys. The wordmark in `partials/header.blade.php` is text; upload a logo in Settings to replace it.
3. `config/home.php` (default copy) and Admin > Pages for wording; `config/site.php` defaults; `config/countries.php` if the market changes.
4. Seeders: tour types, destinations, sample packages, quiz, FAQs (`database/seeders`); replace `database/seed-images` + manifest, and update `IMAGE_CREDITS.md`.
5. Wordmark special-case `GoAustriaTours` in `header.blade.php`; the `Seo::organization()` `areaServed`; the quiz/default text that mentions Austria.
6. Run `npm run build`, `php artisan test`, and Lighthouse.

## Accessibility rules baked in
Visible 3px gold focus ring; 16px inputs (no iOS zoom); labels on every control; `aria-live` on quiz progress and errors; contrast verified (gold text only at 600+ on light); decorative icons `aria-hidden`; reduced-motion respected.

<?php

/**
 * Site-wide defaults and the definition of the admin Settings screen.
 * Values saved in Admin > Settings (settings table) override the defaults here.
 */
return [

    // Seeded on `php artisan db:seed` from .env. Never hard-code credentials.
    'admin_seed' => [
        'email' => env('ADMIN_SEED_EMAIL'),
        'password' => env('ADMIN_SEED_PASSWORD'),
    ],

    // Bump when Terms/Privacy text changes materially; stored with every enquiry as consent evidence.
    'terms_version_default' => '2026-01',

    'defaults' => [
        'brand_name' => 'GoAustriaTours',
        'legal_name' => 'GoAustriaTours',
        'domain' => 'goaustriatours.com',
        'phone' => '',
        'email' => '',
        'address' => '',
        'notification_emails' => '',
        'gtm_id' => '',
        'footer_text' => 'Tailor-made journeys through Austria for discerning travellers.',
        'default_meta_description' => 'Tailor-made Austria tours: self-drive, chauffeur-driven and coach journeys, planned by local experts. Request your free quote.',
        'terms_version' => '2026-01',
        'currency' => 'USD',
        'tour_modal_heading' => 'Get your free personalised quote',
    ],

    // Fields shown on Admin > Settings: group => [key => [label, type, help]]
    'fields' => [
        'Brand' => [
            'brand_name' => ['Brand name', 'text', 'Shown in the header, footer and page titles.'],
            'legal_name' => ['Legal company name', 'text', 'Used in the consent text and legal pages.'],
            'footer_text' => ['Footer text', 'textarea', ''],
            'logo_media_id' => ['Logo', 'media', 'Optional. A text wordmark is used when empty.'],
            'favicon_media_id' => ['Favicon', 'media', 'Optional square image.'],
        ],
        'Contact' => [
            'phone' => ['Phone', 'text', 'Shown as a tap-to-call link. Leave empty to hide.'],
            'email' => ['Public email', 'text', 'Leave empty to hide.'],
            'address' => ['Address', 'textarea', 'Leave empty to hide.'],
            'notification_emails' => ['Enquiry notification emails', 'textarea', 'Comma-separated. New enquiries are emailed here.'],
        ],
        'Social links' => [
            'social_facebook' => ['Facebook URL', 'text', ''],
            'social_instagram' => ['Instagram URL', 'text', ''],
            'social_youtube' => ['YouTube URL', 'text', ''],
            'social_tiktok' => ['TikTok URL', 'text', ''],
        ],
        'Enquiry offer' => [
            'tour_modal_heading' => ['Enquiry pop-up heading', 'text', 'Do not promise a discount unless it is real.'],
        ],
        'SEO' => [
            'default_meta_description' => ['Default meta description', 'textarea', ''],
            'default_og_media_id' => ['Default social share image', 'media', ''],
        ],
        'Tracking & keys' => [
            'gtm_id' => ['Google Tag Manager ID', 'text', 'Like GTM-XXXXXXX. The snippet is only output when set.'],
            'recaptcha_site_key' => ['reCAPTCHA site key', 'text', 'Leave empty to use the value in .env.'],
            'recaptcha_secret_key' => ['reCAPTCHA secret key', 'password', 'Leave empty to keep the current value.'],
            'terms_version' => ['Terms version', 'text', 'Stored with each enquiry. Change when legal text changes.'],
        ],
    ],
];

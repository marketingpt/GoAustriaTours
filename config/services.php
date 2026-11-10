<?php

return [

    'postmark' => ['token' => env('POSTMARK_TOKEN')],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => ['key' => env('RESEND_KEY')],

    // reCAPTCHA v2 checkbox. Admin > Settings overrides these when filled in.
    // Local .env uses Google's public TEST keys; production MUST use real keys.
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    ],

    // GoHighLevel (LeadConnector) API v2. Sync is OFF when token or location is empty.
    'ghl' => [
        'token' => env('GHL_API_TOKEN'),
        'location_id' => env('GHL_LOCATION_ID'),
        'base_url' => 'https://services.leadconnectorhq.com',
        'version' => '2021-07-28',
    ],

];

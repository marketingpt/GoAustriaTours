<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /** Read a site setting (admin > Settings), falling back to config/site.php defaults. */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default ?? config("site.defaults.$key"));
    }
}

if (! function_exists('ghl_enabled')) {
    /** GoHighLevel sync is ON only when both the PIT token and location ID are present in .env. */
    function ghl_enabled(): bool
    {
        return filled(config('services.ghl.token')) && filled(config('services.ghl.location_id'));
    }
}

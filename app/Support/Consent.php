<?php

namespace App\Support;

/**
 * The SMS/terms consent wording. DO NOT reword: it is compliance text and is stored verbatim with every enquiry.
 * The only substitution is the legal company name from Admin > Settings.
 */
class Consent
{
    public static function company(): string
    {
        return (string) setting('legal_name', config('site.defaults.legal_name'));
    }

    public static function version(): string
    {
        return (string) setting('terms_version', config('site.terms_version_default'));
    }

    /** Plain text exactly as shown (and stored). */
    public static function text(?string $company = null): string
    {
        $c = $company ?? self::company();

        return "I agree to the Terms and Conditions and Privacy Policy provided by {$c}. "
            ."By providing my phone number, I consent to receive SMS messages from {$c} regarding my enquiry, booking updates, itinerary updates, reminders, and customer support. "
            .'Message and data rates may apply. Reply STOP to unsubscribe or HELP for assistance. Consent is not a condition of purchase.';
    }

    /** HTML for display: same words, with the two legal names linked (new tab). Escaped. */
    public static function html(): string
    {
        $text = e(self::text());
        $terms = '<a href="'.e(route('terms')).'" target="_blank" rel="noopener" class="underline underline-offset-2 hover:text-gold-700">Terms and Conditions</a>';
        $privacy = '<a href="'.e(route('privacy')).'" target="_blank" rel="noopener" class="underline underline-offset-2 hover:text-gold-700">Privacy Policy</a>';

        return str_replace(['Terms and Conditions and Privacy Policy'], ["{$terms} and {$privacy}"], $text);
    }
}

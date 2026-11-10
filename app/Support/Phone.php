<?php

namespace App\Support;

class Phone
{
    /** Build an E.164 number from a dial code and a nationally formatted number. Null when implausible. */
    public static function e164(string $dial, string $national): ?string
    {
        $dial = preg_replace('/\D+/', '', $dial);
        $digits = preg_replace('/\D+/', '', $national);

        // Drop a pasted-in country code (e.g. "+44 7700...") or trunk zero(s).
        if (str_starts_with(trim($national), '+') && str_starts_with($digits, $dial)) {
            $digits = substr($digits, strlen($dial));
        }
        $digits = ltrim($digits, '0');

        if (strlen($digits) < 6 || strlen($dial.$digits) > 15) {
            return null;
        }

        return '+'.$dial.$digits;
    }
}

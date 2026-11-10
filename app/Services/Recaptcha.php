<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Google reCAPTCHA v2 (checkbox). Fails CLOSED: any error or missing key means "not verified". */
class Recaptcha
{
    public static function siteKey(): ?string
    {
        return setting('recaptcha_site_key') ?: config('services.recaptcha.site_key');
    }

    public static function secretKey(): ?string
    {
        return setting('recaptcha_secret_key') ?: config('services.recaptcha.secret_key');
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        $secret = self::secretKey();
        if (! $token || ! $secret) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(8)->post(config('services.recaptcha.verify_url'), [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            return $response->successful() && $response->json('success') === true;
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification unreachable: '.get_class($e));

            return false;
        }
    }
}

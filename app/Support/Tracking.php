<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/** First-touch / last-touch attribution kept in a 30-day cookie. */
class Tracking
{
    public const COOKIE_FIRST = 'gat_first';

    public const COOKIE_LAST = 'gat_last';

    public const DAYS = 30;

    public const PARAMS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'gbraid', 'wbraid'];

    /** Read params from the current URL; returns [] when none present. */
    public static function fromRequest(Request $request): array
    {
        $out = [];
        foreach (self::PARAMS as $p) {
            $v = $request->query($p);
            if (is_string($v) && $v !== '') {
                $out[$p] = mb_substr(trim($v), 0, 255);
            }
        }

        return $out;
    }

    public static function snapshot(Request $request, array $params): array
    {
        $ref = (string) $request->headers->get('referer', '');
        $own = parse_url($ref, PHP_URL_HOST) === $request->getHost();

        return $params + [
            'referrer' => $own ? '' : mb_substr($ref, 0, 500),
            'landing_page' => mb_substr($request->fullUrl(), 0, 500),
            'captured_at' => now()->toIso8601String(),
        ];
    }

    public static function capture(Request $request, $response): void
    {
        $params = self::fromRequest($request);
        $hasFirst = (bool) self::decode($request->cookie(self::COOKIE_FIRST));
        $minutes = self::DAYS * 24 * 60;

        // First touch: set once. Any visit counts (even with no params, so referrer/landing page are kept).
        if (! $hasFirst) {
            $response->headers->setCookie(Cookie::make(self::COOKIE_FIRST, json_encode(self::snapshot($request, $params)), $minutes, '/', null, $request->isSecure(), true, false, 'Lax'));
        }

        // Last touch: refresh whenever campaign parameters are present.
        if ($params) {
            $response->headers->setCookie(Cookie::make(self::COOKIE_LAST, json_encode(self::snapshot($request, $params)), $minutes, '/', null, $request->isSecure(), true, false, 'Lax'));
        }
    }

    public static function decode(?string $raw): array
    {
        $d = $raw ? json_decode($raw, true) : null;

        return is_array($d) ? $d : [];
    }

    /** [first, last] for an enquiry. Last falls back to first when no newer params were seen. */
    public static function forEnquiry(Request $request): array
    {
        $first = self::decode($request->cookie(self::COOKIE_FIRST));
        $last = self::decode($request->cookie(self::COOKIE_LAST)) ?: $first;

        return [$first, $last];
    }
}

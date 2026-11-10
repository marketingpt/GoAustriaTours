<?php

namespace App\Services\Ghl;

use App\Models\QuizStep;

/**
 * The ONE place that defines GoHighLevel custom field names.
 * Fields are matched by EXACT NAME. To rename a field, rename it in GHL and here together.
 * Staff may move fields into folders in GHL but must not rename them.
 */
class GhlFields
{
    public const TEXT = 'TEXT';

    public const LARGE_TEXT = 'LARGE_TEXT';

    public const PREFERRED_DATE = 'Preferred Travel Date';

    public const SECOND_QUOTE = 'Second Quote Preference';

    public const SPECIAL_REQUEST = 'Special Request';

    public const PACKAGE = 'Package';

    public const FORM_NAME = 'Form Name';

    public const QUIZ_ALL = 'Quiz - All Answers';

    public const CONSENT_GIVEN = 'Consent Given';

    public const CONSENT_AT = 'Consent Timestamp';

    public const CONSENT_VERSION = 'Consent Text Version';

    /** Tracking keys captured for first and last touch. Field names become "<key>_first" / "<key>_last". */
    public const TRACKING_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'gbraid', 'wbraid', 'referrer', 'landing_page'];

    public static function tracking(string $key, string $touch): string
    {
        return "{$key}_{$touch}"; // e.g. utm_source_first, utm_source_last
    }

    public static function quizStep(QuizStep $step): string
    {
        return 'Quiz - '.$step->label;
    }

    /** Every field that must exist in GHL: name => dataType. */
    public static function all(): array
    {
        $fields = [
            self::PREFERRED_DATE => self::TEXT,
            self::SECOND_QUOTE => self::TEXT,
            self::SPECIAL_REQUEST => self::LARGE_TEXT,
            self::PACKAGE => self::TEXT,
            self::FORM_NAME => self::TEXT,
            self::QUIZ_ALL => self::LARGE_TEXT,
            self::CONSENT_GIVEN => self::TEXT,
            self::CONSENT_AT => self::TEXT,
            self::CONSENT_VERSION => self::TEXT,
        ];

        foreach (QuizStep::orderBy('sort_order')->get() as $step) {
            $fields[self::quizStep($step)] = self::TEXT;
        }

        foreach (['first', 'last'] as $touch) {
            foreach (self::TRACKING_KEYS as $key) {
                $fields[self::tracking($key, $touch)] = in_array($key, ['referrer', 'landing_page']) ? self::LARGE_TEXT : self::TEXT;
            }
        }

        return $fields;
    }
}

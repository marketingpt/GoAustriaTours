<?php

namespace App\Services\Ghl;

use App\Models\Enquiry;
use App\Models\QuizStep;

/**
 * Turns an Enquiry into a GHL contact upsert payload.
 * First-touch tracking fields are only sent when the contact does not already have them,
 * so a returning contact keeps its original attribution while *_last fields always refresh.
 */
class GhlPayloadBuilder
{
    /**
     * @param  array<string,string>  $fieldIds  field name => GHL custom field id
     * @param  array<string,mixed>  $existingValues  field id => current value on an existing contact
     */
    public function build(Enquiry $e, array $fieldIds, array $existingValues = []): array
    {
        $values = $this->fieldValues($e);

        $custom = [];
        foreach ($values as $name => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $id = $fieldIds[$name] ?? null;
            if (! $id) {
                continue; // field not created yet (run php artisan ghl:setup)
            }
            if (str_ends_with($name, '_first') && ! empty($existingValues[$id])) {
                continue; // keep original first touch
            }
            $custom[] = ['id' => $id, 'field_value' => (string) $value];
        }

        return [
            'locationId' => (string) config('services.ghl.location_id'),
            'firstName' => $e->first_name,
            'lastName' => $e->last_name,
            'email' => $e->email,
            'phone' => $e->phone_e164,
            'source' => setting('brand_name').' website',
            'tags' => $this->tags($e),
            'customFields' => $custom,
        ];
    }

    /** name => value for every field we send. */
    public function fieldValues(Enquiry $e): array
    {
        $v = [
            GhlFields::PREFERRED_DATE => $e->preferred_date,
            GhlFields::SECOND_QUOTE => $e->second_quote,
            GhlFields::SPECIAL_REQUEST => $e->special_request,
            GhlFields::PACKAGE => $e->package_name,
            GhlFields::FORM_NAME => $e->form_name,
            GhlFields::CONSENT_GIVEN => $e->consent_given ? 'Yes' : 'No',
            GhlFields::CONSENT_AT => $e->consent_at?->toIso8601String(),
            GhlFields::CONSENT_VERSION => $e->terms_version,
        ];

        $answers = $e->quiz_answers ?? [];
        $steps = QuizStep::all()->keyBy('field_key');
        foreach ($answers as $key => $a) {
            if ($steps->has($key)) {
                $v[GhlFields::quizStep($steps[$key])] = $a['answer'];
            }
        }
        if ($answers) {
            $v[GhlFields::QUIZ_ALL] = collect($answers)->map(fn ($a) => "{$a['label']}: {$a['answer']}")->implode("\n");
        }

        foreach (['first' => $e->tracking_first ?? [], 'last' => $e->tracking_last ?? []] as $touch => $data) {
            foreach (GhlFields::TRACKING_KEYS as $key) {
                $v[GhlFields::tracking($key, $touch)] = $data[$key] ?? null;
            }
        }

        return $v;
    }

    public function tags(Enquiry $e): array
    {
        return array_values(array_unique(array_filter([
            setting('brand_name'),
            $e->source ? 'source: '.$e->source : null,
            $e->package_name ? 'package: '.$e->package_name : null,
            'form: '.$e->form_name,
        ])));
    }
}

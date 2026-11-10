<?php

namespace App\Services;

use App\Http\Requests\EnquiryRequest;
use App\Jobs\SyncEnquiryToGhl;
use App\Mail\NewEnquiryMail;
use App\Models\Enquiry;
use App\Models\Package;
use App\Models\QuizStep;
use App\Support\Consent;
use App\Support\Phone;
use App\Support\Tracking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/** Single place where every enquiry form is turned into a saved Enquiry + notifications + CRM sync. */
class EnquiryService
{
    public function create(EnquiryRequest $request): Enquiry
    {
        $countries = config('countries');
        [, $dial] = $countries[$request->input('country')];

        [$first, $last] = Tracking::forEnquiry($request);
        $answers = $this->quizAnswers((array) $request->input('quiz', []));

        $package = $request->filled('package_id') ? Package::find($request->input('package_id')) : null;

        $enquiry = Enquiry::create([
            'form_name' => $request->input('form_name'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => mb_strtolower($request->input('email')),
            'country_code' => $dial,
            'phone_national' => $request->input('phone'),
            'phone_e164' => Phone::e164($dial, $request->input('phone')),
            'preferred_date' => $request->input('preferred_date'),
            'second_quote' => $request->input('second_quote'),
            'special_request' => $request->input('special_request'),
            'time_of_year' => $request->input('time_of_year') ?: ($answers['time_of_year']['answer'] ?? null),
            'group_size' => $request->input('group_size') ?: ($answers['group_size']['answer'] ?? null),
            'nights' => $request->input('nights') ?: ($answers['nights']['answer'] ?? null),
            'package_id' => $package?->id,
            'package_name' => $package?->title,
            'tour_type' => $request->input('tour_type') ?: $package?->tourType?->name,
            'quiz_answers' => $answers ?: null,

            // Consent evidence (what the visitor saw and when)
            'consent_given' => true,
            'consent_text' => Consent::text(),
            'terms_version' => Consent::version(),
            'consent_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
            'page_url' => mb_substr((string) ($request->headers->get('referer') ?: $request->fullUrl()), 0, 1000),

            'source' => $first['utm_source'] ?? (! empty($first['gclid']) ? 'google-ads' : 'direct'),
            'tracking_first' => $first ?: null,
            'tracking_last' => $last ?: null,
            'ghl_status' => ghl_enabled() ? 'pending' : 'disabled',
        ]);

        $this->notify($enquiry);
        $this->syncToCrm($enquiry);

        return $enquiry;
    }

    /** Keep only answers that match an active step and one of its active options. */
    private function quizAnswers(array $input): array
    {
        $out = [];
        foreach (QuizStep::forQuiz() as $step) {
            $given = $input[$step->field_key] ?? null;
            if (is_string($given) && $step->activeOptions->contains('label', $given)) {
                $out[$step->field_key] = ['label' => $step->label, 'question' => $step->question, 'answer' => $given];
            }
        }

        return $out;
    }

    private function notify(Enquiry $enquiry): void
    {
        $to = collect(explode(',', (string) setting('notification_emails', '')))->map('trim')->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))->values()->all();
        if (! $to) {
            Log::info("Enquiry #{$enquiry->id} saved; no notification emails configured.");

            return;
        }
        try {
            Mail::to($to)->queue(new NewEnquiryMail($enquiry));
        } catch (\Throwable $e) {
            Log::error('Enquiry notification failed: '.$e->getMessage());
        }
    }

    private function syncToCrm(Enquiry $enquiry): void
    {
        if (! ghl_enabled()) {
            return;
        }
        try {
            SyncEnquiryToGhl::dispatch($enquiry->id);
        } catch (\Throwable $e) {
            Log::error('GHL dispatch failed: '.$e->getMessage()); // never block the enquiry
        }
    }
}

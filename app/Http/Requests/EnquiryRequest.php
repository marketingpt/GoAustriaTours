<?php

namespace App\Http\Requests;

use App\Services\Recaptcha;
use App\Support\Phone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** One request class for EVERY enquiry form (quiz, modal, package quote, question, contact). */
class EnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($v, $max = 200) => is_string($v) ? mb_substr(trim(strip_tags($v)), 0, $max) : $v;

        $data = [];
        foreach (['form_name', 'first_name', 'last_name', 'email', 'country', 'phone', 'preferred_date', 'second_quote', 'time_of_year', 'group_size', 'nights', 'tour_type', 'package_name'] as $k) {
            if ($this->has($k)) {
                $data[$k] = $clean($this->input($k));
            }
        }
        if ($this->has('special_request')) {
            $data['special_request'] = $clean($this->input('special_request'), 2000);
        }
        if (is_array($this->input('quiz'))) {
            $data['quiz'] = array_map(fn ($v) => is_string($v) ? $clean($v, 100) : '', $this->input('quiz'));
        }
        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'form_name' => ['required', 'string', 'max:80'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'country' => ['required', Rule::in(array_keys(config('countries')))],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+\-\s().]{6,25}$/'],
            'preferred_date' => ['nullable', 'string', 'max:60'],
            'second_quote' => ['nullable', Rule::in(['yes', 'no', 'maybe'])],
            'special_request' => ['nullable', 'string', 'max:2000'],
            'time_of_year' => ['nullable', 'string', 'max:60'],
            'group_size' => ['nullable', 'string', 'max:60'],
            'nights' => ['nullable', 'string', 'max:60'],
            'tour_type' => ['nullable', 'string', 'max:80'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'quiz' => ['nullable', 'array', 'max:30'],
            'quiz.*' => ['nullable', 'string', 'max:100'],
            'consent' => ['accepted'],
            'g-recaptcha-response' => ['required', 'string'],
            // honeypot: must stay empty
            'company_website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Please enter your phone number.',
            'phone.regex' => 'Please enter a valid phone number.',
            'country.in' => 'Please choose a country code.',
            'consent.accepted' => 'Please tick the box to agree to the terms and SMS consent before submitting.',
            'g-recaptcha-response.required' => 'Please tick "I\'m not a robot".',
            'company_website.max' => 'Your submission could not be processed.',
        ];
    }

    /** Phone plausibility and reCAPTCHA run only once the basics pass (so the one-time token is not wasted). */
    public function after(): array
    {
        return [function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $dial = config('countries')[$this->input('country')][1];
            if (! Phone::e164($dial, $this->input('phone'))) {
                $v->errors()->add('phone', 'Please enter a valid phone number.');

                return;
            }

            if (! app(Recaptcha::class)->verify($this->input('g-recaptcha-response'), $this->ip())) {
                $v->errors()->add('g-recaptcha-response', 'We could not verify you are human. Please tick "I\'m not a robot" and try again.');
            }
        }];
    }
}

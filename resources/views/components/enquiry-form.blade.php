@props([
    'formId',                       // unique per form on the page (ids, error scoping)
    'formName' => 'Enquiry',        // stored with the enquiry and sent to GHL/dataLayer
    'package' => null,              // Package model for "Get a Quote" forms
    'tourType' => null,
    'extended' => false,            // adds time of year / group size / nights
    'dynamic' => false,             // modal: hidden fields bound to the modal's Alpine state
    'embedded' => false,            // quiz: lives inside the quiz's Alpine scope
    'quiz' => false,                // include quiz answers as hidden fields
    'submitLabel' => 'Request my free quote',
    'siteKey' => \App\Services\Recaptcha::siteKey(),
])
@php
    $mine = old('_form') === $formId;                                  // only repopulate/show server errors on the form that was submitted
    $val = fn ($k, $d = '') => $mine ? old($k, $d) : $d;
    $err = fn ($k) => $mine ? $errors->first($k) : '';
    $countries = config('countries');
    $seasons = ['Spring', 'Summer', 'Autumn', 'Winter'];
@endphp

<form
    action="{{ route('enquiry.store') }}" method="POST" id="{{ $formId }}" data-enquiry
    @if (! $embedded) x-data="enquiryForm()" @endif
    x-on:submit.prevent="submit()"
    @if ($mine && $errors->any()) data-has-server-errors @endif
    class="space-y-4" aria-label="{{ $formName }}"
>
    @csrf
    <input type="hidden" name="_form" value="{{ $formId }}">
    @if ($dynamic)
        <input type="hidden" name="form_name" x-bind:value="form_name">
        <input type="hidden" name="package_id" x-bind:value="package_id">
        <input type="hidden" name="package_name" x-bind:value="package_name">
        <input type="hidden" name="tour_type" x-bind:value="tour_type">
    @else
        <input type="hidden" name="form_name" value="{{ $formName }}">
        @if ($package)
            <input type="hidden" name="package_id" value="{{ $package->id }}">
            <input type="hidden" name="package_name" value="{{ $package->title }}">
        @endif
        @if ($tourType)
            <input type="hidden" name="tour_type" value="{{ $tourType }}">
        @endif
    @endif
    @if ($quiz)
        <template x-for="(v, k) in answers" :key="k"><input type="hidden" :name="'quiz[' + k + ']'" :value="v"></template>
    @endif

    {{-- Honeypot --}}
    <div class="hp-field" aria-hidden="true">
        <label for="{{ $formId }}-hp">Leave this field empty</label>
        <input type="text" id="{{ $formId }}-hp" name="company_website" value="" tabindex="-1" autocomplete="off">
    </div>

    <div x-show="!done" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-form-field name="first_name" label="First name" :id="$formId.'-first'" required :error="$err('first_name')">
                <input class="input" type="text" id="{{ $formId }}-first" name="first_name" value="{{ $val('first_name') }}" required autocomplete="given-name" maxlength="80" @input="clear('first_name')" :aria-invalid="errors.first_name ? 'true' : 'false'">
            </x-form-field>
            <x-form-field name="last_name" label="Last name" :id="$formId.'-last'" required :error="$err('last_name')">
                <input class="input" type="text" id="{{ $formId }}-last" name="last_name" value="{{ $val('last_name') }}" required autocomplete="family-name" maxlength="80" @input="clear('last_name')" :aria-invalid="errors.last_name ? 'true' : 'false'">
            </x-form-field>
        </div>

        <x-form-field name="email" label="Email" :id="$formId.'-email'" required :error="$err('email')">
            <input class="input" type="email" id="{{ $formId }}-email" name="email" value="{{ $val('email') }}" required autocomplete="email" maxlength="190" @input="clear('email')" :aria-invalid="errors.email ? 'true' : 'false'">
        </x-form-field>

        <div>
            <label for="{{ $formId }}-phone" class="mb-1 block text-sm font-medium text-ink">Phone<span class="text-cta-700" aria-hidden="true"> *</span></label>
            <div class="flex gap-2">
                <select name="country" id="{{ $formId }}-country" class="input !w-32 shrink-0 sm:!w-44" aria-label="Country code" autocomplete="country">
                    @foreach ($countries as $iso => [$cname, $dial])
                        <option value="{{ $iso }}" @selected($val('country', 'US') === $iso)>{{ $iso }} +{{ $dial }}@if(false) {{ $cname }}@endif</option>
                    @endforeach
                </select>
                <input class="input" type="tel" inputmode="tel" id="{{ $formId }}-phone" name="phone" value="{{ $val('phone') }}" required autocomplete="tel-national" maxlength="25" placeholder="Phone number" @input="clear('phone')" :aria-invalid="errors.phone ? 'true' : 'false'">
            </div>
            @if ($err('phone')) <p class="mt-1 text-sm font-medium text-red-700" role="alert">{{ $err('phone') }}</p> @endif
            <p x-cloak x-show="errors.phone || errors.country" x-text="((errors.phone || errors.country) || [])[0]" class="mt-1 text-sm font-medium text-red-700" role="alert"></p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-form-field name="preferred_date" label="Preferred travel date (approx.)" :id="$formId.'-date'" :error="$err('preferred_date')">
                <input class="input" type="month" id="{{ $formId }}-date" name="preferred_date" value="{{ $val('preferred_date') }}" min="{{ now()->format('Y-m') }}" placeholder="YYYY-MM">
            </x-form-field>
            <x-form-field name="second_quote" label="Would you like a second quote from a partner?" :id="$formId.'-second'" :error="$err('second_quote')">
                <select class="input" id="{{ $formId }}-second" name="second_quote">
                    <option value="">Please choose</option>
                    <option value="yes" @selected($val('second_quote') === 'yes')>Yes, please</option>
                    <option value="no" @selected($val('second_quote') === 'no')>No, thank you</option>
                </select>
            </x-form-field>
        </div>

        @if ($extended)
            <div class="grid gap-4 sm:grid-cols-3">
                <x-form-field name="time_of_year" label="Time of year" :id="$formId.'-season'">
                    <select class="input" id="{{ $formId }}-season" name="time_of_year">
                        <option value="">Select</option>
                        @foreach ($seasons as $s)<option @selected($val('time_of_year') === $s)>{{ $s }}</option>@endforeach
                    </select>
                </x-form-field>
                <x-form-field name="group_size" label="Group size" :id="$formId.'-group'">
                    <select class="input" id="{{ $formId }}-group" name="group_size">
                        <option value="">Select</option>
                        @foreach (range(1, 9) as $n)<option @selected($val('group_size') === ($n === 1 ? '1 person' : "$n people"))>{{ $n === 1 ? '1 person' : "$n people" }}</option>@endforeach
                        <option @selected($val('group_size') === '10+ people')>10+ people</option>
                    </select>
                </x-form-field>
                <x-form-field name="nights" label="Nights" :id="$formId.'-nights'">
                    <select class="input" id="{{ $formId }}-nights" name="nights">
                        <option value="">Select</option>
                        @foreach (range(3, 13) as $n)<option @selected($val('nights') === "$n nights")>{{ $n }} nights</option>@endforeach
                        <option @selected($val('nights') === '14+ nights')>14+ nights</option>
                    </select>
                </x-form-field>
            </div>
        @endif

        <x-form-field name="special_request" label="Special request" :id="$formId.'-request'" :error="$err('special_request')">
            <textarea class="input" id="{{ $formId }}-request" name="special_request" rows="3" maxlength="2000" placeholder="Tell us anything that will help us plan your trip">{{ $val('special_request') }}</textarea>
        </x-form-field>

        {{-- Consent (REQUIRED, never pre-ticked). Wording lives in App\Support\Consent. --}}
        <div>
            <div class="flex items-start gap-3 rounded-lg bg-sand/70 p-3">
                <input type="checkbox" id="{{ $formId }}-consent" name="consent" value="1" required class="mt-1 h-5 w-5 shrink-0 rounded border-brand-400 accent-brand-700" @input="clear('consent')" :aria-invalid="errors.consent ? 'true' : 'false'" aria-describedby="{{ $formId }}-consent-text">
                <label for="{{ $formId }}-consent" id="{{ $formId }}-consent-text" class="text-xs leading-relaxed text-ink-soft">{!! \App\Support\Consent::html() !!}</label>
            </div>
            @if ($err('consent')) <p class="mt-1 text-sm font-medium text-red-700" role="alert">{{ $err('consent') }}</p> @endif
            <p x-cloak x-show="errors.consent" x-text="(errors.consent || [])[0]" class="mt-1 text-sm font-medium text-red-700" role="alert"></p>
        </div>

        {{-- reCAPTCHA v2 checkbox (explicit rendering; script loads on first interaction) --}}
        <div>
            <div class="gat-recaptcha min-h-[78px]" data-sitekey="{{ $siteKey }}"></div>
            <noscript>
                @if ($siteKey)
                    <iframe src="https://www.google.com/recaptcha/api/fallback?k={{ $siteKey }}" title="reCAPTCHA" height="422" width="302" style="border:0"></iframe>
                    <textarea name="g-recaptcha-response" required class="input mt-2" placeholder="Paste the code from the box above"></textarea>
                @endif
            </noscript>
            @if ($err('g-recaptcha-response')) <p class="mt-1 text-sm font-medium text-red-700" role="alert">{{ $err('g-recaptcha-response') }}</p> @endif
            <p x-cloak x-show="errors['g-recaptcha-response']" x-text="(errors['g-recaptcha-response'] || [])[0]" class="mt-1 text-sm font-medium text-red-700" role="alert"></p>
            @if (! $siteKey) <p class="mt-1 text-sm font-medium text-red-700">reCAPTCHA is not configured (set the site key in Admin &gt; Settings or .env).</p> @endif
        </div>

        <p x-cloak x-show="formError" x-text="formError" class="rounded-lg bg-red-50 p-3 text-sm font-medium text-red-800" role="alert"></p>
        @if ($mine && $errors->any() && ! $errors->hasAny(['first_name','last_name','email','phone','consent','g-recaptcha-response']))
            <p class="rounded-lg bg-red-50 p-3 text-sm font-medium text-red-800" role="alert">{{ $errors->first() }}</p>
        @endif

        <x-button type="submit" size="lg" class="w-full" x-bind:disabled="submitting">
            <span x-show="!submitting">{{ $submitLabel }}</span>
            <span x-cloak x-show="submitting">Sending…</span>
        </x-button>
    </div>

    {{-- Success state (shown briefly before redirecting to the Thank You page) --}}
    <div x-cloak x-show="done" class="rounded-xl bg-brand-50 p-6 text-center" role="status" aria-live="polite">
        <x-icon name="check" class="mx-auto mb-3 h-10 w-10 rounded-full bg-brand-700 p-2 text-white"/>
        <p class="font-display text-2xl font-semibold text-brand-800">Thank you, we have your enquiry.</p>
        <p class="mt-1 text-sm text-ink-soft">Taking you to the next step…</p>
    </div>
</form>

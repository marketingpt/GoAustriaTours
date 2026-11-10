@props(['steps'])
@php
    $total = $steps->count();
    $meta = $steps->map(fn ($s) => ['key' => $s->field_key, 'label' => $s->label])->values();
@endphp
<section id="start-your-adventure" aria-labelledby="quiz-title" x-data="quizForm({ total: {{ $total }} })"
         class="rounded-2xl border-[3px] border-cta-500 bg-white p-5 text-ink shadow-lift sm:p-7">
    <div class="text-center">
        <h2 id="quiz-title" class="text-3xl uppercase tracking-wide sm:text-4xl">Start Your Adventure</h2>
        <div class="js-only mt-3">
            <div class="h-1.5 overflow-hidden rounded-full bg-brand-100" role="progressbar" aria-label="Quiz progress" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="progress">
                <div class="h-full rounded-full bg-cta-500 transition-all duration-300" :style="`width:${Math.max(progress, 6)}%`"></div>
            </div>
            <p class="mt-1.5 text-xs font-medium text-ink-soft" aria-live="polite" x-text="onContact ? 'Last step: where should we send your quote?' : `Question ${step + 1} of ${total}`"></p>
        </div>
    </div>

    <div class="mt-5">
        @foreach ($steps as $i => $step)
            <div x-cloak x-show="step === {{ $i }}" class="js-only" role="group" aria-labelledby="quiz-q-{{ $i }}">
                <h3 id="quiz-q-{{ $i }}" class="text-center font-display text-2xl font-semibold leading-snug text-brand-800">{{ $step->question }}</h3>

                @if ($step->type === 'tiles')
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        @foreach ($step->activeOptions as $opt)
                            <button type="button" @click="pick({{ Js::from($step->field_key) }}, {{ Js::from($opt->label) }})"
                                    :aria-pressed="answers[{{ Js::from($step->field_key) }}] === {{ Js::from($opt->label) }}"
                                    :class="answers[{{ Js::from($step->field_key) }}] === {{ Js::from($opt->label) }} ? 'border-cta-500 bg-cta-500/10' : 'border-brand-100 bg-white'"
                                    class="flex min-h-[5.5rem] flex-col items-center justify-center gap-2 rounded-xl border-2 p-3 text-center text-sm font-semibold leading-tight text-brand-800 transition hover:border-cta-500 hover:bg-cta-500/5">
                                <x-icon :name="$opt->icon ?: 'sparkles'" class="h-7 w-7 text-brand-600"/>
                                <span>{{ $opt->label }}</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <label for="quiz-select-{{ $i }}" class="sr-only">{{ $step->question }}</label>
                    <select id="quiz-select-{{ $i }}" class="input mt-4" x-model="answers[{{ Js::from($step->field_key) }}]">
                        <option value="">Select…</option>
                        @foreach ($step->activeOptions as $opt)<option value="{{ $opt->label }}">{{ $opt->label }}</option>@endforeach
                    </select>
                    <x-button class="mt-4 w-full" x-bind:disabled="!answers[{{ Js::from($step->field_key) }}]" @click="next({{ Js::from($step->field_key) }})">Next</x-button>
                @endif

                @if ($i > 0)
                    <button type="button" @click="back()" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-ink-soft underline-offset-4 hover:text-brand-700 hover:underline"><x-icon name="arrow-left" class="h-4 w-4"/> Back</button>
                @endif
            </div>
        @endforeach

        {{-- Final step. Also the ONLY thing shown when JavaScript is off, so no lead is lost. --}}
        <div x-cloak x-show="onContact" class="keep-nojs">
            <div class="js-only">
                <button type="button" @click="back()" class="mb-3 inline-flex items-center gap-1 text-sm font-medium text-ink-soft underline-offset-4 hover:text-brand-700 hover:underline"><x-icon name="arrow-left" class="h-4 w-4"/> Back</button>
                <ul class="mb-4 flex flex-wrap gap-2" aria-label="Your answers">
                    <template x-for="a in summary({{ Js::from($meta) }})" :key="a.label">
                        <li class="rounded-full bg-sand px-3 py-1 text-xs font-medium text-brand-800"><span class="text-ink-soft" x-text="a.label + ':'"></span> <span x-text="a.value"></span></li>
                    </template>
                </ul>
            </div>
            <p class="mb-4 text-center font-display text-xl font-semibold text-brand-800">Where should we send your quote?</p>
            <x-enquiry-form form-id="quiz-form" form-name="Hero Quiz" embedded quiz submit-label="Get my free quote"/>
        </div>
    </div>

    <p class="mt-4 flex items-center justify-center gap-1.5 text-xs text-ink-soft"><x-icon name="lock" class="h-3.5 w-3.5"/> Takes less than 30 seconds</p>
</section>

@extends('layouts.site')

@php
    $t = fn ($key) => $home->text($key);
    $mediaOf = fn ($key) => ($id = $home->content[$key] ?? null) ? \App\Models\Media::find($id) : null;
    $heroMedia = $preload ?? null;
    $whyMedia = $mediaOf('why_media_id');
    $bandMedia = $mediaOf('band_media_id');
    $badgeIcons = ['shield', 'compass', 'user'];
@endphp

@section('content')
{{-- 3. HERO --}}
<section class="relative isolate overflow-hidden bg-brand-900 text-white">
    @if ($heroMedia)
        <x-picture :media="$heroMedia" eager :width="1600" sizes="100vw" class="absolute inset-0 -z-20 h-full w-full object-cover"/>
    @endif
    <div class="hero-scrim absolute inset-0 -z-10" aria-hidden="true"></div>

    <div class="container-x grid items-center gap-10 py-12 lg:grid-cols-[1.1fr_0.9fr] lg:gap-14 lg:py-24">
        <div>
            <p class="eyebrow mb-4 !text-gold-300">{{ $t('hero_eyebrow') }}</p>
            <h1 class="text-[2.75rem] !text-white sm:text-6xl lg:text-7xl">{{ $t('hero_title') }} <em class="font-medium italic text-gold-200">{{ $t('hero_title_accent') }}</em></h1>
            <p class="mt-6 max-w-xl text-lg text-white/90 sm:text-xl">{{ $t('hero_sub') }}</p>
            <ul class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach ([1, 2, 3] as $n)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gold-300/60 text-gold-300"><x-icon :name="$badgeIcons[$n - 1]" class="h-5 w-5"/></span>
                        <span><span class="block text-sm font-semibold text-white">{{ $t("badge_{$n}_title") }}</span><span class="block text-xs text-white/75">{{ $t("badge_{$n}_text") }}</span></span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>
            <x-quiz-card :steps="$steps"/>
        </div>
    </div>
</section>

{{-- 4. WHY TRAVELLERS CHOOSE US --}}
<section class="section-y" aria-labelledby="why-title">
    <div class="container-x grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
        <div class="relative order-2 lg:order-1">
            <x-picture :media="$whyMedia" sizes="(min-width:1024px) 560px, 100vw" :width="960" class="aspect-[4/5] w-full rounded-[var(--radius-card)] object-cover shadow-lift"/>
            <div class="absolute -bottom-5 -right-3 hidden h-32 w-32 rounded-full border border-gold-400 sm:block" aria-hidden="true"></div>
        </div>
        <div class="order-1 lg:order-2">
            <x-section-heading align="left" :eyebrow="$t('why_eyebrow')" :title="$t('why_title')" id="why-title"/>
            <ol class="space-y-7">
                @foreach ([1, 2, 3, 4] as $n)
                    <li class="flex gap-5">
                        <span class="font-display text-4xl italic leading-none text-gold-600" aria-hidden="true">0{{ $n }}</span>
                        <div>
                            <h3 class="text-2xl">{{ $t("why_{$n}_title") }}</h3>
                            <p class="mt-1 text-ink-soft">{{ $t("why_{$n}_text") }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

{{-- 5. TOUR TYPES --}}
<section class="section-y bg-sand/60" aria-labelledby="types-title" id="travel-styles">
    <div class="container-x">
        <x-section-heading :eyebrow="$t('types_eyebrow')" :title="$t('types_title')" :text="$t('types_text')"/>
        <div class="grid gap-7 md:grid-cols-3">
            @foreach ($tourTypes as $type)
                @php
                    $quote = ['form_name' => "Tour Type Quote", 'heading' => "Get a quote: {$type->name}", 'tour_type' => $type->name];
                    $ask = ['form_name' => 'Tour Type Question', 'heading' => "Ask us about {$type->name}", 'intro' => 'Tell us what you would like to know and a specialist will reply.', 'tour_type' => $type->name];
                @endphp
                <article class="flex flex-col overflow-hidden rounded-[var(--radius-card)] bg-white shadow-card">
                    <x-picture :media="$type->media" sizes="(min-width:768px) 380px, 100vw" :width="640" class="aspect-[16/11] w-full object-cover"/>
                    <div class="flex flex-1 flex-col p-6">
                        <p class="eyebrow">{{ $type->tagline }}</p>
                        <h3 class="mt-1 text-3xl">{{ $type->name }}</h3>
                        <p class="mt-3 flex-1 text-sm text-ink-soft">{{ $type->description }}</p>
                        <div class="mt-6 flex flex-col gap-2 sm:flex-row md:flex-col xl:flex-row">
                            <x-button size="sm" class="flex-1" x-data @click="$dispatch('open-enquiry', {{ Js::from($quote) }})">Get a Quote</x-button>
                            <x-button size="sm" variant="outline" class="flex-1" x-data @click="$dispatch('open-enquiry', {{ Js::from($ask) }})">Ask Us a Question</x-button>
                        </div>
                        <a href="{{ route('packages.index', ['type' => $type->slug]) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700 hover:text-gold-700">See {{ strtolower($type->name) }} journeys <x-icon name="arrow-right" class="h-4 w-4"/></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 6. FEATURED PACKAGES --}}
@if ($featured->isNotEmpty())
<section class="section-y" aria-labelledby="packages-title">
    <div class="container-x">
        <x-section-heading :eyebrow="$t('packages_eyebrow')" :title="$t('packages_title')" :text="$t('packages_text')"/>
        <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $package)<x-package-card :package="$package"/>@endforeach
        </div>
        <p class="mt-10 text-center"><x-button :href="route('packages.index')" variant="outline" icon="arrow-right">View all journeys</x-button></p>
    </div>
</section>
@endif

{{-- 7. HOW IT WORKS --}}
<section class="section-y bg-brand-800 text-white" aria-labelledby="how-title">
    <div class="container-x">
        <x-section-heading light :eyebrow="$t('how_eyebrow')" :title="$t('how_title')"/>
        <ol class="grid gap-10 md:grid-cols-3">
            @foreach ([1, 2, 3] as $n)
                <li class="relative text-center">
                    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-gold-400 font-display text-3xl italic text-gold-300">{{ $n }}</span>
                    <h3 class="mt-5 text-2xl !text-white">{{ $t("how_{$n}_title") }}</h3>
                    <p class="mx-auto mt-2 max-w-xs text-white/80">{{ $t("how_{$n}_text") }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- Full-width image band --}}
<section class="relative isolate overflow-hidden bg-brand-900 py-24 text-center text-white sm:py-32" aria-label="{{ $t('band_title') }}">
    @if ($bandMedia)<x-picture :media="$bandMedia" sizes="100vw" class="absolute inset-0 -z-20 h-full w-full object-cover"/>@endif
    <div class="absolute inset-0 -z-10 bg-brand-900/60" aria-hidden="true"></div>
    <div class="container-x">
        <p class="font-display text-4xl italic sm:text-6xl">{{ $t('band_title') }}</p>
        <p class="mx-auto mt-4 max-w-xl text-lg text-white/90">{{ $t('band_text') }}</p>
        <x-button class="mt-8" size="lg" x-data @click="$dispatch('open-enquiry', { form_name: 'Image Band Button' })">Request my free quote</x-button>
    </div>
</section>

{{-- 8. TESTIMONIALS --}}
@if ($testimonials->isNotEmpty())
<section class="section-y" aria-labelledby="testimonials-title">
    <div class="container-x">
        <x-section-heading :eyebrow="$t('testimonials_eyebrow')" :title="$t('testimonials_title')"/>
        <div class="grid gap-7 md:grid-cols-3">
            @foreach ($testimonials as $review)
                <figure class="flex h-full flex-col rounded-[var(--radius-card)] bg-white p-7 shadow-card">
                    <x-icon name="quote" class="h-8 w-8 text-gold-400"/>
                    <blockquote class="mt-3 flex-1 text-ink-soft">{{ $review->quote }}</blockquote>
                    <figcaption class="mt-5 border-t border-brand-100 pt-4 text-sm">
                        <span class="font-semibold text-brand-800">{{ $review->name }}</span>@if ($review->location), <span class="text-ink-soft">{{ $review->location }}</span>@endif
                        @if ($review->is_placeholder)<span class="ml-2 rounded bg-gold-100 px-2 py-0.5 text-[0.65rem] font-semibold uppercase tracking-wider text-gold-800">Placeholder</span>@endif
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 9. FAQ --}}
@if ($faqs->isNotEmpty())
<section class="section-y bg-sand/60" aria-labelledby="faq-title" id="faq">
    <div class="container-x grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
        <x-section-heading align="left" :eyebrow="$t('faq_eyebrow')" :title="$t('faq_title')" class="lg:sticky lg:top-28 lg:self-start" id="faq-title"/>
        <div>@foreach ($faqs as $faq)<x-faq-item :question="$faq->question">{{ $faq->answer }}</x-faq-item>@endforeach</div>
    </div>
</section>
@endif

{{-- 10. FINAL CALL TO ACTION --}}
<section class="section-y bg-brand-700 text-center text-white" aria-labelledby="cta-title">
    <div class="container-x max-w-3xl">
        <h2 id="cta-title" class="text-4xl !text-white sm:text-5xl">{{ $t('cta_title') }}</h2>
        <p class="mt-4 text-lg text-white/85">{{ $t('cta_text') }}</p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <x-button size="lg" x-data @click="$dispatch('open-enquiry', { form_name: 'Final CTA Button' })">Get my free quote</x-button>
            @if (setting('phone'))<x-button size="lg" variant="outline-light" :href="'tel:'.preg_replace('/[^\d+]/', '', setting('phone'))" icon="phone">Call {{ setting('phone') }}</x-button>@endif
        </div>
    </div>
</section>
@endsection

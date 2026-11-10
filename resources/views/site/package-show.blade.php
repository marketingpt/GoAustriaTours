@extends('layouts.site')

@php
    $images = $package->gallery->isNotEmpty() ? $package->gallery : collect([$package->media])->filter();
    $hero = $images->first();
    $ask = ['form_name' => 'Package Question', 'heading' => 'Ask us about this journey', 'intro' => $package->title, 'package_id' => $package->id, 'package_name' => $package->title, 'tour_type' => $package->tourType?->name];
@endphp
@isset($hero) @php $preload = $hero; @endphp @endisset

@section('content')
<section class="bg-brand-800 py-10 text-white sm:py-14">
    <div class="container-x">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs text-white/70">
            <a href="{{ route('home') }}" class="hover:text-white">Home</a> / <a href="{{ route('packages.index') }}" class="hover:text-white">Journeys</a> / <span>{{ $package->title }}</span>
        </nav>
        <p class="eyebrow !text-gold-300">{{ $package->tourType?->name }}@if ($package->destination) · {{ $package->destination->name }}@endif · {{ $package->nights }} nights</p>
        <h1 class="mt-2 max-w-4xl text-4xl !text-white sm:text-6xl">{{ $package->title }}</h1>
        @if ($package->summary)<p class="mt-4 max-w-3xl text-lg text-white/85">{{ $package->summary }}</p>@endif
    </div>
</section>

<div class="container-x grid gap-10 py-10 lg:grid-cols-[1fr_24rem] lg:gap-14 lg:py-14">
    <div class="min-w-0 space-y-12">
        @if ($package->is_sample)
            <p class="rounded-lg border border-gold-300 bg-gold-50 p-4 text-sm text-gold-800" role="note"><strong>Sample journey.</strong> This itinerary, inclusions and price are placeholder content and will be replaced with confirmed details.</p>
        @endif

        {{-- Gallery --}}
        @if ($images->isNotEmpty())
            <div x-data="{ current: 0 }" class="space-y-3">
                @foreach ($images as $i => $img)
                    <div x-show="current === {{ $i }}" @if ($i > 0) x-cloak @endif>
                        <x-picture :media="$img" :eager="$i === 0" :width="1600" sizes="(min-width:1024px) 720px, 100vw" class="aspect-[16/10] w-full rounded-[var(--radius-card)] object-cover shadow-card"/>
                    </div>
                @endforeach
                @if ($images->count() > 1)
                    <ul class="grid grid-cols-5 gap-2">
                        @foreach ($images as $i => $img)
                            <li><button type="button" @click="current = {{ $i }}" :aria-current="current === {{ $i }}" class="block w-full overflow-hidden rounded-lg border-2" :class="current === {{ $i }} ? 'border-gold-500' : 'border-transparent'" aria-label="Show image {{ $i + 1 }}"><x-picture :media="$img" :width="480" sizes="120px" class="aspect-[4/3] w-full object-cover"/></button></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        @if ($package->highlights)
            <section aria-labelledby="highlights-title">
                <h2 id="highlights-title" class="text-3xl">Highlights</h2>
                <ul class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach ($package->highlights as $h)<li class="flex items-start gap-3"><x-icon name="check" class="mt-1 h-5 w-5 shrink-0 text-gold-600"/><span>{{ $h }}</span></li>@endforeach
                </ul>
            </section>
        @endif

        @if ($package->description)
            <section aria-labelledby="overview-title">
                <h2 id="overview-title" class="text-3xl">Overview</h2>
                <div class="prose-content mt-4">{!! $package->description !!}</div>
            </section>
        @endif

        @if ($package->itinerary)
            <section aria-labelledby="itinerary-title">
                <h2 id="itinerary-title" class="mb-3 text-3xl">Day by day</h2>
                @foreach ($package->itinerary as $day)<x-faq-item :question="$day['title'] ?? ''">{{ $day['body'] ?? '' }}</x-faq-item>@endforeach
            </section>
        @endif

        @if ($package->inclusions || $package->exclusions)
            <section class="grid gap-8 sm:grid-cols-2" aria-label="What is included">
                @if ($package->inclusions)
                    <div><h2 class="text-2xl">Included</h2><ul class="mt-3 space-y-2">@foreach ($package->inclusions as $x)<li class="flex gap-2"><x-icon name="check" class="mt-1 h-4 w-4 shrink-0 text-brand-500"/>{{ $x }}</li>@endforeach</ul></div>
                @endif
                @if ($package->exclusions)
                    <div><h2 class="text-2xl">Not included</h2><ul class="mt-3 space-y-2">@foreach ($package->exclusions as $x)<li class="flex gap-2"><x-icon name="x" class="mt-1 h-4 w-4 shrink-0 text-ink-soft"/>{{ $x }}</li>@endforeach</ul></div>
                @endif
            </section>
        @endif

        @if ($package->faqs)
            <section aria-labelledby="pkg-faq-title">
                <h2 id="pkg-faq-title" class="mb-3 text-3xl">Questions about this journey</h2>
                @foreach ($package->faqs as $faq)<x-faq-item :question="$faq['question'] ?? ''">{{ $faq['answer'] ?? '' }}</x-faq-item>@endforeach
            </section>
        @endif
    </div>

    {{-- Sticky quote card --}}
    <aside class="lg:sticky lg:top-28 lg:self-start" aria-label="Request a quote" id="quote">
        <x-card class="border-t-4 border-cta-500">
            @if ($package->priceLabel())
                <p class="text-xs uppercase tracking-widest text-ink-soft">From</p>
                <p class="font-display text-5xl font-semibold text-brand-800">{{ $package->priceLabel() }}<span class="ml-1 text-sm font-sans font-normal text-ink-soft">per person</span></p>
                @if ($package->price_note)<p class="mt-1 text-xs text-ink-soft">{{ $package->price_note }}</p>@endif
            @endif
            <h2 class="mt-4 text-2xl">Get a free quote</h2>
            <p class="mb-4 text-sm text-ink-soft">No obligation. A specialist will tailor this journey to you.</p>
            <x-enquiry-form form-id="package-form" form-name="Package Quote" :package="$package" :tour-type="$package->tourType?->name" extended submit-label="Request my free quote"/>
            <button type="button" class="mt-4 w-full text-center text-sm font-semibold text-brand-700 underline underline-offset-4 hover:text-gold-700" x-data @click="$dispatch('open-enquiry', {{ Js::from($ask) }})">Or ask us a question</button>
        </x-card>
    </aside>
</div>

@if ($related->isNotEmpty())
    <section class="section-y bg-sand/60" aria-labelledby="related-title">
        <div class="container-x">
            <x-section-heading title="More journeys you may like" id="related-title"/>
            <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">@foreach ($related as $r)<x-package-card :package="$r"/>@endforeach</div>
        </div>
    </section>
@endif
@endsection

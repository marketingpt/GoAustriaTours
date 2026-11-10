@extends('layouts.site')

@section('content')
<section class="bg-brand-800 py-14 text-center text-white sm:py-20">
    <div class="container-x">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs text-white/70"><a href="{{ route('home') }}" class="hover:text-white">Home</a> / <span>Destinations</span></nav>
        <h1 class="text-4xl !text-white sm:text-6xl">Austria Destinations</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-white/85">From imperial Vienna to alpine lakes: the places our journeys are built around.</p>
    </div>
</section>
<section class="section-y">
    <div class="container-x grid gap-7 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($destinations as $d)
            <a href="{{ route('destinations.show', $d->slug) }}" class="group block overflow-hidden rounded-[var(--radius-card)] bg-white shadow-card transition hover:-translate-y-1 hover:shadow-lift">
                <div class="overflow-hidden"><x-picture :media="$d->media" sizes="(min-width:1024px) 400px, 100vw" :width="640" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105"/></div>
                <div class="p-6">
                    <p class="eyebrow">{{ $d->tagline }}</p>
                    <h2 class="mt-1 text-3xl">{{ $d->name }}</h2>
                    <p class="mt-2 text-sm text-ink-soft">{{ $d->intro }}</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700">Explore {{ $d->name }} <x-icon name="arrow-right" class="h-4 w-4"/></span>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endsection

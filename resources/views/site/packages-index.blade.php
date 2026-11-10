@extends('layouts.site')

@section('content')
<section class="bg-brand-800 py-14 text-center text-white sm:py-20">
    <div class="container-x">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs text-white/70"><a href="{{ route('home') }}" class="hover:text-white">Home</a> / <span>Journeys</span></nav>
        <h1 class="text-4xl !text-white sm:text-6xl">{{ $heading }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-white/85">{{ $activeType?->description ?: 'Every journey is a starting point. Tell us what you would like to change and we will tailor it to you.' }}</p>
    </div>
</section>

<section class="section-y">
    <div class="container-x">
        <div class="mb-10 space-y-4 rounded-[var(--radius-card)] bg-white p-5 shadow-card sm:p-6">
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-2 text-sm font-semibold text-brand-800">Travel style</span>
                <a href="{{ route('packages.index', array_filter(['destination' => $activeDestination?->slug])) }}" @class(['rounded-full border px-4 py-1.5 text-sm', 'border-brand-700 bg-brand-700 text-white' => ! $activeType, 'border-brand-200 text-ink hover:border-brand-500' => $activeType])>All</a>
                @foreach ($tourTypes as $tt)
                    <a href="{{ route('packages.index', array_filter(['type' => $tt->slug, 'destination' => $activeDestination?->slug])) }}" @class(['rounded-full border px-4 py-1.5 text-sm', 'border-brand-700 bg-brand-700 text-white' => $activeType?->id === $tt->id, 'border-brand-200 text-ink hover:border-brand-500' => $activeType?->id !== $tt->id])>{{ $tt->name }}</a>
                @endforeach
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-2 text-sm font-semibold text-brand-800">Destination</span>
                <a href="{{ route('packages.index', array_filter(['type' => $activeType?->slug])) }}" @class(['rounded-full border px-4 py-1.5 text-sm', 'border-brand-700 bg-brand-700 text-white' => ! $activeDestination, 'border-brand-200 text-ink hover:border-brand-500' => $activeDestination])>All</a>
                @foreach ($destinations as $d)
                    <a href="{{ route('packages.index', array_filter(['type' => $activeType?->slug, 'destination' => $d->slug])) }}" @class(['rounded-full border px-4 py-1.5 text-sm', 'border-brand-700 bg-brand-700 text-white' => $activeDestination?->id === $d->id, 'border-brand-200 text-ink hover:border-brand-500' => $activeDestination?->id !== $d->id])>{{ $d->name }}</a>
                @endforeach
            </div>
        </div>

        @if ($packages->isEmpty())
            <div class="rounded-[var(--radius-card)] bg-white p-10 text-center shadow-card">
                <p class="font-display text-2xl text-brand-800">No journeys match those filters yet.</p>
                <p class="mt-2 text-ink-soft">Tell us what you have in mind and we will build it for you.</p>
                <x-button class="mt-6" x-data @click="$dispatch('open-enquiry', { form_name: 'Packages Empty State' })">Get a free quote</x-button>
            </div>
        @else
            <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)<x-package-card :package="$package" heading="h2"/>@endforeach
            </div>
        @endif
    </div>
</section>
@endsection

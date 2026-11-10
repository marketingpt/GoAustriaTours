@extends('layouts.site')

@php
    if ($destination->media) { $preload = $destination->media; }
    $modal = ['form_name' => 'Destination Quote', 'heading' => "Plan your {$destination->name} trip", 'tour_type' => null];
@endphp

@section('content')
<section class="relative isolate overflow-hidden bg-brand-900 py-20 text-white sm:py-32">
    @if ($destination->media)<x-picture :media="$destination->media" eager :width="1600" sizes="100vw" class="absolute inset-0 -z-20 h-full w-full object-cover"/>@endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-900/90 via-brand-900/40 to-brand-900/30" aria-hidden="true"></div>
    <div class="container-x">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs text-white/75"><a href="{{ route('home') }}" class="hover:text-white">Home</a> / <a href="{{ route('destinations.index') }}" class="hover:text-white">Destinations</a> / <span>{{ $destination->name }}</span></nav>
        <p class="eyebrow !text-gold-300">{{ $destination->tagline }}</p>
        <h1 class="mt-2 text-5xl !text-white sm:text-7xl">{{ $destination->name }}</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/90">{{ $destination->intro }}</p>
        <x-button class="mt-8" size="lg" x-data @click="$dispatch('open-enquiry', {{ Js::from($modal) }})">Plan my {{ $destination->name }} trip</x-button>
    </div>
</section>

@if ($destination->body)
    <section class="section-y"><div class="container-x max-w-3xl"><div class="prose-content text-lg">{!! $destination->body !!}</div></div></section>
@endif

@if ($packages->isNotEmpty())
    <section class="section-y bg-sand/60" aria-labelledby="dest-packages">
        <div class="container-x">
            <x-section-heading :title="'Journeys featuring '.$destination->name" id="dest-packages"/>
            <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">@foreach ($packages as $p)<x-package-card :package="$p"/>@endforeach</div>
        </div>
    </section>
@endif

<section class="section-y" aria-labelledby="other-dest">
    <div class="container-x">
        <x-section-heading title="Other destinations" id="other-dest"/>
        <ul class="flex flex-wrap justify-center gap-3">@foreach ($others as $o)<li><a href="{{ route('destinations.show', $o->slug) }}" class="rounded-full border border-brand-200 bg-white px-5 py-2 text-sm font-medium hover:border-brand-600">{{ $o->name }}</a></li>@endforeach</ul>
    </div>
</section>
@endsection

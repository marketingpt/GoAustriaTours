@extends('layouts.site')

@section('content')
<section class="bg-brand-800 py-14 text-center text-white sm:py-20">
    <div class="container-x">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs text-white/70"><a href="{{ route('home') }}" class="hover:text-white">Home</a> / <span>{{ $page->title }}</span></nav>
        <h1 class="text-4xl !text-white sm:text-6xl">{{ $page->title }}</h1>
    </div>
</section>
<section class="section-y">
    <div class="container-x max-w-3xl">
        <div class="prose-content text-lg">{!! $body !!}</div>
        @unless ($legal ?? false)
            <div class="mt-12 rounded-[var(--radius-card)] bg-brand-800 p-8 text-center text-white">
                <p class="font-display text-3xl">Ready to plan your trip?</p>
                <x-button class="mt-5" x-data @click="$dispatch('open-enquiry', { form_name: 'About Page Button' })">Get a free quote</x-button>
            </div>
        @endunless
    </div>
</section>
@endsection

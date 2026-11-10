@extends('layouts.site')

@php $phone = setting('phone'); $email = setting('email'); $address = setting('address'); @endphp

@section('content')
<section class="bg-brand-800 py-14 text-center text-white sm:py-20">
    <div class="container-x">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs text-white/70"><a href="{{ route('home') }}" class="hover:text-white">Home</a> / <span>Contact</span></nav>
        <h1 class="text-4xl !text-white sm:text-6xl">{{ $page->title }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-white/85">{{ $page->text('intro') }}</p>
    </div>
</section>
<section class="section-y">
    <div class="container-x grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">
        <div class="space-y-6">
            <h2 class="text-3xl">Talk to a specialist</h2>
            <ul class="space-y-4 text-ink-soft">
                @if ($phone)<li class="flex items-start gap-3"><x-icon name="phone" class="mt-1 h-5 w-5 text-gold-600"/><a class="font-semibold text-brand-800 hover:text-gold-700" href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a></li>@endif
                @if ($email)<li class="flex items-start gap-3"><x-icon name="mail" class="mt-1 h-5 w-5 text-gold-600"/><a class="font-semibold text-brand-800 hover:text-gold-700" href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
                @if ($address)<li class="flex items-start gap-3"><x-icon name="map" class="mt-1 h-5 w-5 text-gold-600"/><span class="whitespace-pre-line">{{ $address }}</span></li>@endif
                @unless ($phone || $email || $address)<li>Use the form and we will get back to you.</li>@endunless
            </ul>
            <p class="text-sm text-ink-soft">We will use your details only to respond to your enquiry. See our <a class="underline" href="{{ route('privacy') }}">Privacy Policy</a>.</p>
        </div>
        <x-card>
            <h2 class="mb-5 text-3xl">Send us a message</h2>
            <x-enquiry-form form-id="contact-form" form-name="Contact Page" extended submit-label="Send my enquiry"/>
        </x-card>
    </div>
</section>
@endsection

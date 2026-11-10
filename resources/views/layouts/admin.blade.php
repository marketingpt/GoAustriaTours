<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} | {{ setting('brand_name') }} Admin</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen text-ink" x-data="{ nav: false }">
@php
    $links = [
        ['Dashboard', 'admin.dashboard', 'compass'], ['Enquiries', 'admin.enquiries.index', 'mail'], ['Packages', 'admin.packages.index', 'map'],
        ['Tour types', 'admin.tour-types.index', 'car'], ['Destinations', 'admin.destinations.index', 'mountain'], ['Quiz', 'admin.quiz.index', 'question'],
        ['Pages', 'admin.pages.index', 'sparkles'], ['Testimonials', 'admin.testimonials.index', 'quote'], ['FAQs', 'admin.faqs.index', 'question'],
        ['Media', 'admin.media.index', 'sun'], ['Settings', 'admin.settings.edit', 'shield'], ['Admin users', 'admin.users.index', 'users'],
    ];
@endphp
<div class="flex min-h-screen">
    <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-y-auto bg-brand-900 p-4 text-white/85 transition lg:static lg:translate-x-0" :class="{ '!translate-x-0': nav }" aria-label="Admin navigation">
        <p class="mb-6 px-2 font-display text-2xl text-white">{{ setting('brand_name') }}<span class="block text-xs font-sans uppercase tracking-widest text-gold-300">Admin</span></p>
        <nav class="space-y-1">
            @foreach ($links as [$label, $route, $icon])
                @php $active = $route === 'admin.dashboard' ? request()->routeIs('admin.dashboard') : request()->routeIs(Str::beforeLast($route, '.').'.*'); @endphp
                <a href="{{ route($route) }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2 text-sm', 'bg-white/10 text-white' => $active, 'hover:bg-white/5' => ! $active])><x-icon :name="$icon" class="h-4 w-4"/>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="mt-8 border-t border-white/10 pt-4 text-sm">
            <a href="{{ route('home') }}" target="_blank" class="block px-3 py-2 hover:text-white">View website ↗</a>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="w-full rounded-lg px-3 py-2 text-left hover:bg-white/5">Log out ({{ auth('admin')->user()->name }})</button></form>
        </div>
    </aside>
    <div x-show="nav" x-cloak class="fixed inset-0 z-30 bg-black/40 lg:hidden" @click="nav = false"></div>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-brand-100 bg-white/90 px-4 py-3 backdrop-blur lg:hidden">
            <button type="button" @click="nav = !nav" class="rounded p-2 hover:bg-brand-50" aria-label="Menu"><x-icon name="menu" class="h-6 w-6"/></button>
            <span class="font-display text-xl">{{ $title ?? 'Admin' }}</span>
        </header>
        <main class="mx-auto max-w-6xl p-4 sm:p-8">
            @if (session('status'))<p class="mb-5 rounded-lg border border-brand-300 bg-brand-50 p-3 text-sm font-medium text-brand-800" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<div class="mb-5 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800" role="alert"><p class="font-semibold">Please fix the following:</p><ul class="mt-1 list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>

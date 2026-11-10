@php
    $seo = $seo ?? \App\Support\Seo::make(setting('brand_name'));
    $gtm = preg_match('/^GTM-[A-Z0-9]+$/', (string) setting('gtm_id')) ? setting('gtm_id') : null;
    $faviconMedia = ($id = setting('favicon_media_id')) ? \App\Models\Media::find($id) : null;
    $jsonld = array_merge([\App\Support\Seo::organization()], $seo['jsonld'] ?? []);
@endphp
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>document.documentElement.className='js';window.dataLayer=window.dataLayer||[];</script>

    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <link rel="canonical" href="{{ $seo['canonical'] }}">
    @if ($seo['noindex'])<meta name="robots" content="noindex, follow">@else<meta name="robots" content="index, follow, max-image-preview:large">@endif

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ setting('brand_name') }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ $seo['canonical'] }}">
    @if ($seo['image'])<meta property="og:image" content="{{ $seo['image'] }}">@endif
    <meta name="twitter:card" content="{{ $seo['image'] ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    @if ($seo['image'])<meta name="twitter:image" content="{{ $seo['image'] }}">@endif
    <meta name="theme-color" content="#1f3a2e">

    @if ($faviconMedia)
        <link rel="icon" href="{{ $faviconMedia->url(480) }}" type="image/webp">
    @else
        <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%231f3a2e'/%3E%3Ctext x='32' y='43' font-size='34' text-anchor='middle' fill='%23d9b873' font-family='Georgia,serif'%3EG%3C/text%3E%3C/svg%3E">
    @endif

    {{-- Preload the two fonts used above the fold, and the hero image when the page supplies one --}}
    @unless (app()->environment('local') && file_exists(public_path('hot')))
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('resources/fonts/inter-latin-wght-normal.woff2') }}">
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('resources/fonts/cormorant-garamond-latin-wght-normal.woff2') }}">
    @endunless
    @isset($preload)
        <link rel="preload" as="image" fetchpriority="high" href="{{ $preload->url(960) }}" imagesrcset="{{ $preload->srcset() }}" imagesizes="100vw">
    @endisset

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if ($gtm)
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtm }}');</script>
    @endif

    @foreach ($jsonld as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endforeach
</head>
<body class="min-h-screen antialiased">
    @if ($gtm)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
    @endif

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[200] focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-brand-800">Skip to content</a>

    @include('partials.topbar')
    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')

    @unless ($noModal ?? false)
        @include('partials.enquiry-modal')
    @endunless

    @stack('scripts')
</body>
</html>

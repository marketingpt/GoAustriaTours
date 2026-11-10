@extends('layouts.site', ['seo' => \App\Support\Seo::make('Page not found', 'The page you were looking for could not be found.', null, true)])

@section('content')
<section class="section-y">
    <div class="container-x max-w-2xl text-center">
        <p class="eyebrow">Error 404</p>
        <h1 class="mt-3 text-5xl sm:text-6xl">We can't find that page</h1>
        <p class="mt-5 text-lg text-ink-soft">It may have moved or no longer exists. Let's get you back on the road.</p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <x-button :href="route('home')">Go to the home page</x-button>
            <x-button :href="route('packages.index')" variant="outline">Browse journeys</x-button>
        </div>
    </div>
</section>
@endsection

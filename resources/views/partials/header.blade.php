@php
    $logo = ($id = setting('logo_media_id')) ? \App\Models\Media::find($id) : null;
    $brand = setting('brand_name');
    $defaultLogo = asset('images/austria-logo.png');
@endphp
<header
    class="site-header sticky top-0 z-40"
    x-data="{ mobile: false, scrolled: false }"
    x-init="const onScroll = () => { scrolled = window.scrollY > 12; }; onScroll(); window.addEventListener('scroll', onScroll, { passive: true });"
    :class="{ 'site-header--scrolled': scrolled }"
    @keydown.escape.window="mobile = false"
>
    <div class="site-header__inner container-x flex items-center justify-between gap-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center" aria-label="{{ $brand }} home">
            @if ($logo)
                <img src="{{ $logo->url(480) }}" width="{{ $logo->width }}" height="{{ $logo->height }}" alt="{{ $brand }}" class="site-header__logo site-header__logo-img">
            @elseif (file_exists(public_path('images/austria-logo.png')))
                <img src="{{ $defaultLogo }}" alt="{{ $brand }}" class="site-header__logo site-header__logo-img">
            @else
                <span class="font-display text-[1.7rem] font-semibold leading-none tracking-tight text-brand-800">{{ $brand === 'GoAustriaTours' ? 'GoAustria' : $brand }}@if ($brand === 'GoAustriaTours')<span class="italic text-gold-600">Tours</span>@endif</span>
            @endif
        </a>

        {{-- Desktop navigation --}}
        <nav class="site-header__nav hidden items-center gap-1 lg:flex" aria-label="Main">
            @foreach ([['Travel styles', $navTourTypes, fn ($i) => route('packages.index', ['type' => $i->slug])], ['Destinations', $navDestinations, fn ($i) => route('destinations.show', $i->slug)]] as [$label, $items, $url])
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @focusin="open = true" @focusout="if (!$el.contains($event.relatedTarget)) open = false" @keydown.escape="open = false" @click.outside="open = false">
                    <button type="button" class="inline-flex items-center gap-1 rounded px-3 py-2 text-sm font-medium hover:text-gold-700" :aria-expanded="open" aria-haspopup="true" @click="open = true">
                        {{ $label }}<x-icon name="chevron-down" class="h-4 w-4"/>
                    </button>
                    <div x-cloak x-show="open" x-transition.opacity class="absolute left-0 top-full z-50 w-64 pt-2">
                        <ul class="rounded-xl bg-white p-2 shadow-lift">
                            @foreach ($items as $item)
                                <li><a href="{{ $url($item) }}" class="block rounded-lg px-3 py-2 text-sm text-ink hover:bg-brand-50 hover:text-brand-700">{{ $item->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
            <a href="{{ route('packages.index') }}" class="rounded px-3 py-2 text-sm font-medium hover:text-gold-700">Journeys</a>
            <a href="{{ route('about') }}" class="rounded px-3 py-2 text-sm font-medium hover:text-gold-700">About</a>
            <a href="{{ route('contact') }}" class="rounded px-3 py-2 text-sm font-medium hover:text-gold-700">Contact</a>
        </nav>

        <div class="flex items-center gap-2">
            <x-button size="sm" class="site-header__action hidden sm:inline-flex" x-data @click="$dispatch('open-enquiry', { form_name: 'Header Button' })">Get a Quote</x-button>
            <button type="button" class="site-header__menu-button rounded-lg p-2 hover:bg-brand-50/20 lg:hidden" @click="mobile = !mobile" :aria-expanded="mobile" aria-controls="mobile-menu" aria-label="Menu">
                <x-icon name="menu" class="h-6 w-6"/>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <nav id="mobile-menu" x-cloak x-show="mobile" x-transition.opacity class="border-t border-brand-100 bg-ivory lg:hidden" aria-label="Mobile">
        <div class="container-x space-y-5 py-5">
            <div>
                <p class="eyebrow mb-2">Travel styles</p>
                <ul class="space-y-1">@foreach ($navTourTypes as $t)<li><a class="block py-1.5 text-base" href="{{ route('packages.index', ['type' => $t->slug]) }}">{{ $t->name }}</a></li>@endforeach</ul>
            </div>
            <div>
                <p class="eyebrow mb-2">Destinations</p>
                <ul class="space-y-1">@foreach ($navDestinations as $d)<li><a class="block py-1.5 text-base" href="{{ route('destinations.show', $d->slug) }}">{{ $d->name }}</a></li>@endforeach</ul>
            </div>
            <ul class="space-y-1 border-t border-brand-100 pt-4">
                <li><a class="block py-1.5 text-base" href="{{ route('packages.index') }}">Journeys</a></li>
                <li><a class="block py-1.5 text-base" href="{{ route('about') }}">About</a></li>
                <li><a class="block py-1.5 text-base" href="{{ route('contact') }}">Contact</a></li>
            </ul>
            <x-button class="w-full" @click="mobile = false; $dispatch('open-enquiry', { form_name: 'Mobile Menu Button' })">Get a Quote</x-button>
        </div>
    </nav>
</header>

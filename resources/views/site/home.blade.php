@extends('layouts.site')

@php
    $t = fn ($key) => $home->text($key);
    $mediaOf = fn ($key) => ($id = $home->content[$key] ?? null) ? \App\Models\Media::find($id) : null;
    $heroMedia = $preload ?? null;
    $heroVideoUrl = 'https://player.vimeo.com/external/449928180.sd.mp4?s=6d4c4d7e3f95a5a1d5f0b11b6d30f4d2d8d7d2a0&profile_id=164&oauth2_token_id=57447761';
    $whyMedia = $mediaOf('why_media_id');
    $bandMedia = $mediaOf('band_media_id');
    $badgeIcons = ['shield', 'compass', 'user'];
    $heroDestinations = ['Vienna', 'Salzburg', 'Hallstatt', 'Innsbruck', 'Wachau', 'Zell am See', 'Graz'];
@endphp

@section('content')
{{-- 3. HERO --}}
<section class="relative isolate overflow-hidden bg-brand-900 text-white">
    @if ($heroVideoUrl)
        <video class="hero-video absolute inset-0 -z-20 h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $heroMedia ? $heroMedia->url(1600) : '' }}">
            <source src="{{ $heroVideoUrl }}" type="video/mp4">
        </video>
    @elseif ($heroMedia)
        <x-picture :media="$heroMedia" eager :width="1600" sizes="100vw" class="absolute inset-0 -z-20 h-full w-full object-cover"/>
    @endif
    <div class="hero-scrim absolute inset-0 -z-10" aria-hidden="true"></div>

    <div class="container-x grid items-center gap-8 pt-[180px] pb-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12 lg:pt-[210px] lg:pb-12">
        <div>
            <p class="eyebrow mb-3 !text-gold-300">{{ $t('hero_eyebrow') }}</p>
            <h1 class="max-w-2xl text-5xl !text-white sm:text-6xl lg:text-7xl">{{ $t('hero_title') }} <em class="font-medium italic text-gold-200">{{ $t('hero_title_accent') }}</em></h1>
            <p class="mt-4 max-w-xl text-base leading-relaxed text-white/90 sm:text-lg">{{ $t('hero_sub') }}</p>
            <ul class="mt-6 grid gap-4">
                @foreach ([1, 2, 3] as $n)
                    <li class="flex items-start gap-2.5">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-gold-300/60 text-gold-300"><x-icon :name="$badgeIcons[$n - 1]" class="h-4 w-4"/></span>
                        <span class="min-w-0 space-y-1"><span class="block text-sm font-semibold leading-snug text-white">{{ $t("badge_{$n}_title") }}</span><span class="block text-xs leading-relaxed text-white/75">{{ $t("badge_{$n}_text") }}</span></span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>
            <x-quiz-card :steps="$steps"/>
        </div>
    </div>

    <div class="hero-destinations" role="region" aria-label="Popular Austria destinations">
        <p class="hero-destinations__label">Explore Austria</p>
        <div class="hero-destinations__viewport">
            <div class="hero-destinations__track">
                @foreach (range(0, 3) as $duplicate)
                    <ul class="hero-destinations__group" @if ($duplicate > 0) aria-hidden="true" @endif>
                        @foreach ($heroDestinations as $destination)
                            <li>{{ $destination }}</li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- 4. WHY TRAVELLERS CHOOSE US --}}
<section class="section-y" aria-labelledby="why-title">
    <div class="container-x grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
        <div class="relative order-2 lg:order-1">
            <x-picture :media="$whyMedia" sizes="(min-width:1024px) 560px, 100vw" :width="960" class="aspect-[4/5] w-full rounded-[var(--radius-card)] object-cover shadow-lift"/>
            <div class="absolute -bottom-5 -right-3 hidden h-32 w-32 rounded-full border border-gold-400 sm:block" aria-hidden="true"></div>
        </div>
        <div class="order-1 lg:order-2">
            <x-section-heading align="left" :eyebrow="$t('why_eyebrow')" :title="$t('why_title')" id="why-title"/>
            <ol class="space-y-7">
                @foreach ([1, 2, 3, 4] as $n)
                    <li class="flex gap-5">
                        <span class="font-display text-4xl italic leading-none text-gold-600" aria-hidden="true">0{{ $n }}</span>
                        <div>
                            <h3 class="text-2xl">{{ $t("why_{$n}_title") }}</h3>
                            <p class="mt-1 text-ink-soft">{{ $t("why_{$n}_text") }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

{{-- 5. TOUR TYPES --}}
<section class="section-y bg-sand/60" aria-labelledby="types-title" id="travel-styles">
    <div class="container-x">
        <x-section-heading :eyebrow="$t('types_eyebrow')" :title="$t('types_title')" :text="$t('types_text')"/>
        <div class="grid gap-7 md:grid-cols-3">
            @foreach ($tourTypes as $type)
                @php
                    $quote = ['form_name' => "Tour Type Quote", 'heading' => "Get a quote: {$type->name}", 'tour_type' => $type->name];
                    $ask = ['form_name' => 'Tour Type Question', 'heading' => "Ask us about {$type->name}", 'intro' => 'Tell us what you would like to know and a specialist will reply.', 'tour_type' => $type->name];
                @endphp
                <article class="flex flex-col overflow-hidden rounded-[var(--radius-card)] bg-white shadow-card">
                    <x-picture :media="$type->media" sizes="(min-width:768px) 380px, 100vw" :width="640" class="aspect-[16/11] w-full object-cover"/>
                    <div class="flex flex-1 flex-col p-6">
                        <p class="eyebrow">{{ $type->tagline }}</p>
                        <h3 class="mt-1 text-3xl">{{ $type->name }}</h3>
                        <p class="mt-3 flex-1 text-sm text-ink-soft">{{ $type->description }}</p>
                        <div class="mt-6 flex flex-col gap-2 sm:flex-row md:flex-col xl:flex-row">
                            <x-button size="sm" class="flex-1" x-data @click="$dispatch('open-enquiry', {{ Js::from($quote) }})">Get a Quote</x-button>
                            <x-button size="sm" variant="outline" class="flex-1" x-data @click="$dispatch('open-enquiry', {{ Js::from($ask) }})">Ask Us a Question</x-button>
                        </div>
                        <a href="{{ route('packages.index', ['type' => $type->slug]) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700 hover:text-gold-700">See {{ strtolower($type->name) }} journeys <x-icon name="arrow-right" class="h-4 w-4"/></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 6. TOP DESTINATIONS --}}
<section class="section-y bg-[radial-gradient(circle_at_top,#f7f1e5,transparent_40%),#f7f3ee]" aria-labelledby="top-destinations-title">
    <div class="container-x">
        <div class="mb-8 max-w-3xl">
            <p class="eyebrow">Top destinations</p>
            <h2 id="top-destinations-title" class="mt-3 text-4xl leading-tight text-brand-800 sm:text-5xl">From Alpine peaks <em class="font-medium italic text-gold-600">to imperial streets.</em></h2>
            <p class="mt-4 max-w-2xl text-lg text-ink-soft">Discover Austria’s breathtaking landscapes, charming villages, and historic cities, each revealing a different side of its timeless beauty.</p>
        </div>

        <div class="mb-10">
            <x-button size="lg" x-data @click="$dispatch('open-enquiry', { form_name: 'Explore Trips CTA' })">Explore Trips</x-button>
        </div>

        @php
            $topDestinations = [
                [
                    'title' => 'The Grand Austrian Journey',
                    'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80',
                    'items' => ['Vienna', 'Wachau', 'Salzburg', 'Hallstatt', 'Gosau', 'Innsbruck'],
                ],
                [
                    'title' => 'Imperial & Alpine',
                    'image' => 'https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=1200&q=80',
                    'items' => ['Vienna', 'Melk', 'Linz', 'Salzburg', 'Zell am See', 'Innsbruck'],
                ],
                [
                    'title' => 'Lakeside Austria Escape',
                    'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80',
                    'items' => ['Salzburg', 'St. Gilgen', 'St. Wolfgang', 'Hallstatt', 'Gosau', 'Bad Ischl'],
                ],
                [
                    'title' => 'Danube & Vineyards',
                    'image' => 'https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&w=1200&q=80',
                    'items' => ['Vienna', 'Krems', 'Dürnstein', 'Melk', 'Linz', 'Salzburg'],
                ],
                [
                    'title' => 'The Alpine Discovery',
                    'image' => 'https://images.unsplash.com/photo-1521295121783-8a321d551ad2?auto=format&fit=crop&w=1200&q=80',
                    'items' => ['Innsbruck', 'Seefeld', 'Ötztal', 'Zell am See', 'Kaprun', 'Salzburg'],
                ],
                [
                    'title' => 'Castles & Countryside',
                    'image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=1200&q=80',
                    'items' => ['Vienna', 'Graz', 'Klagenfurt', 'Villach', 'Innsbruck', 'Salzburg'],
                ],
            ];
        @endphp

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($topDestinations as $destination)
                <article class="group relative min-h-[22rem] overflow-hidden rounded-[var(--radius-card)] shadow-card">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: linear-gradient(180deg, rgb(15 29 23 / 0.1), rgb(15 29 23 / 0.72)), url('{{ $destination['image'] }}');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-900/90 via-brand-900/20 to-transparent"></div>

                    <div class="relative z-10 flex h-full flex-col justify-end p-5 text-white">
                        <h3 class="text-[1.8rem] leading-tight text-white">{{ $destination['title'] }}</h3>
                        <ul class="mt-4 grid grid-cols-2 gap-x-3 gap-y-1 text-[0.78rem] text-white/85">
                            @foreach ($destination['items'] as $place)
                                <li class="flex items-center gap-2"><span class="text-gold-300">•</span><span>{{ $place }}</span></li>
                            @endforeach
                        </ul>
                        <div class="mt-5">
                            <x-button variant="outline-light" size="sm" x-data @click="$dispatch('open-enquiry', { form_name: 'Explore Trip', heading: '{{ $destination['title'] }}' })">Explore Now</x-button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 7. AUSTRIA, FRAME BY FRAME --}}
<section class="section-y bg-brand-900 text-white" aria-labelledby="austria-frame-title">
    <div class="container-x">
        <div class="mb-8 max-w-4xl">
            <h2 id="austria-frame-title" class="text-4xl leading-tight text-white sm:text-5xl">Austria, <em class="font-medium italic text-gold-200">frame by frame.</em></h2>
            <p class="mt-4 max-w-3xl text-lg text-white/80">From majestic Alpine landscapes to charming historic towns, discover Austria’s most captivating destinations, unforgettable experiences, and hidden gems.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-5">
            @php
                $frameTiles = [
                    ['title' => 'Lakeside Serenity', 'name' => 'Hallstatt', 'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80'],
                    ['title' => 'Alpine Beauty', 'name' => 'Innsbruck', 'image' => 'https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=1200&q=80'],
                    ['title' => 'Baroque Charm', 'name' => 'Salzburg', 'image' => 'https://images.unsplash.com/photo-1521295121783-8a321d551ad2?auto=format&fit=crop&w=1200&q=80'],
                    ['title' => 'Vineyard Escapes', 'name' => 'Wachau', 'image' => 'https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&w=1200&q=80'],
                    ['title' => 'Imperial Elegance', 'name' => 'Vienna', 'image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=1200&q=80'],
                ];
            @endphp

            @foreach ($frameTiles as $tile)
                <article class="group relative overflow-hidden rounded-[var(--radius-card)] border border-white/10 bg-white/5 min-h-[16.5rem] shadow-lift">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: linear-gradient(180deg, rgb(15 29 23 / 0.1), rgb(15 29 23 / 0.75)), url('{{ $tile['image'] }}');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-900 via-brand-900/30 to-transparent"></div>
                    <div class="relative z-10 flex h-full flex-col justify-end p-4 text-white">
                        <p class="text-[0.62rem] font-semibold uppercase tracking-[0.18em] text-gold-300">{{ $tile['title'] }}</p>
                        <h3 class="mt-2 text-[2rem] leading-none text-white">{{ $tile['name'] }}</h3>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 8. WHY TRAVEL WITH US / USP --}}
<section class="section-y bg-sand/60" aria-labelledby="why-travel-title">
    <div class="container-x grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start lg:gap-16">
        <div class="lg:pr-6">
            <p class="eyebrow">WHY TRAVEL WITH US</p>
            <h2 id="why-travel-title" class="mt-3 text-4xl leading-tight text-brand-800 sm:text-5xl">Discover Austria <em class="font-medium italic text-gold-600">Differently.</em></h2>
            <p class="mt-5 max-w-xl text-lg text-ink-soft">Discover Austria’s breathtaking landscapes, charming villages, and historic cities, each revealing a different side of its timeless beauty.</p>
            <p class="mt-8"><x-button size="lg" x-data @click="$dispatch('open-enquiry', { form_name: 'USP CTA' })">Get a free quote</x-button></p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-[var(--radius-card)] bg-white p-6 shadow-card">
                <p class="text-base font-semibold text-brand-800">Local Expertise</p>
                <p class="mt-3 text-sm leading-relaxed text-ink-soft">Expert destination knowledge and recommendations for an authentic Austrian experience.</p>
            </div>
            <div class="rounded-[var(--radius-card)] bg-white p-6 shadow-card">
                <p class="text-base font-semibold text-brand-800">Alpine Escapes</p>
                <p class="mt-3 text-sm leading-relaxed text-ink-soft">Explore Austria’s mountains, lakes, and picturesque countryside through unforgettable scenic routes.</p>
            </div>
            <div class="rounded-[var(--radius-card)] bg-white p-6 shadow-card">
                <p class="text-base font-semibold text-brand-800">Cultural Immersion</p>
                <p class="mt-3 text-sm leading-relaxed text-ink-soft">Discover imperial palaces, classical music, traditional cuisine, and local Alpine culture.</p>
            </div>
            <div class="rounded-[var(--radius-card)] bg-white p-6 shadow-card">
                <p class="text-base font-semibold text-brand-800">Bespoke Journeys</p>
                <p class="mt-3 text-sm leading-relaxed text-ink-soft">Personalized itineraries designed around your interests, travel style, and budget.</p>
            </div>
        </div>
    </div>
</section>

{{-- 9. TRAVEL WITH CONFIDENCE --}}
<section class="section-y bg-[radial-gradient(circle_at_top,#fbf6ee,#f7f3ee_55%,#f2efe9)]" aria-labelledby="travel-confidence-title">
    <div class="container-x max-w-6xl">
        <div class="text-center">
            <p class="eyebrow text-brand-700">TRAVEL WITH CONFIDENCE</p>
            <h2 id="travel-confidence-title" class="mt-3 text-4xl leading-tight text-brand-800 sm:text-5xl">Seamless Payments <em class="font-medium italic text-gold-600">Simple, Secure &amp; Stress-Free.</em></h2>
            <p class="mx-auto mt-5 max-w-3xl text-lg text-ink-soft">Make booking your Austria getaway simple and stress-free with secure payments, flexible options, and hassle-free confirmations.</p>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[var(--radius-card)] bg-white p-5 shadow-card">
                <x-icon name="wallet" class="mb-4 h-10 w-10 text-gold-700"/>
                <p class="text-base font-semibold text-brand-800">Individual Payments</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Each traveller pays their share online with ease.</p>
            </div>
            <div class="rounded-[var(--radius-card)] bg-white p-5 shadow-card">
                <x-icon name="shield" class="mb-4 h-10 w-10 text-gold-700"/>
                <p class="text-base font-semibold text-brand-800">Secure Transactions</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Enjoy safe and reliable payments from anywhere.</p>
            </div>
            <div class="rounded-[var(--radius-card)] bg-white p-5 shadow-card">
                <x-icon name="check" class="mb-4 h-10 w-10 text-gold-700"/>
                <p class="text-base font-semibold text-brand-800">Instant Confirmation</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Receive booking confirmations straight to your inbox.</p>
            </div>
            <div class="rounded-[var(--radius-card)] bg-white p-5 shadow-card">
                <x-icon name="calendar" class="mb-4 h-10 w-10 text-gold-700"/>
                <p class="text-base font-semibold text-brand-800">Flexible Options</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Choose convenient payment options that suit your group.</p>
            </div>
        </div>

        <div class="mt-10 text-center">
            <x-button size="lg" x-data @click="$dispatch('open-enquiry', { form_name: 'Payment CTA' })">Get a free quote</x-button>
        </div>
    </div>
</section>

{{-- 10. FEATURED PACKAGES --}}
@if ($featured->isNotEmpty())
<section class="section-y" aria-labelledby="packages-title">
    <div class="container-x">
        <x-section-heading :eyebrow="$t('packages_eyebrow')" :title="$t('packages_title')" :text="$t('packages_text')"/>
        <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $package)<x-package-card :package="$package"/>@endforeach
        </div>
        <p class="mt-10 text-center"><x-button :href="route('packages.index')" variant="outline" icon="arrow-right">View all journeys</x-button></p>
    </div>
</section>
@endif

{{-- 9. HOW IT WORKS --}}
<section class="section-y bg-brand-800 text-white" aria-labelledby="how-title">
    <div class="container-x">
        <x-section-heading light :eyebrow="$t('how_eyebrow')" :title="$t('how_title')"/>
        <ol class="grid gap-10 md:grid-cols-3">
            @foreach ([1, 2, 3] as $n)
                <li class="relative text-center">
                    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-gold-400 font-display text-3xl italic text-gold-300">{{ $n }}</span>
                    <h3 class="mt-5 text-2xl !text-white">{{ $t("how_{$n}_title") }}</h3>
                    <p class="mx-auto mt-2 max-w-xs text-white/80">{{ $t("how_{$n}_text") }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- 8. TESTIMONIALS --}}
@if ($testimonials->isNotEmpty())
<section class="section-y" aria-labelledby="testimonials-title">
    <div class="container-x">
        <x-section-heading :eyebrow="$t('testimonials_eyebrow')" :title="$t('testimonials_title')"/>
        @if ($testimonials->contains(fn ($review) => $review->is_placeholder))
            <p class="-mt-5 mb-6 text-center text-sm text-ink-soft">Quotes, names and locations shown here are fictional examples, not actual customer reviews.</p>
        @endif
        <div class="grid gap-7 md:grid-cols-3">
            @foreach ($testimonials as $review)
                <figure class="flex h-full flex-col rounded-[var(--radius-card)] bg-white p-7 shadow-card">
                    <x-icon name="quote" class="h-8 w-8 text-gold-400"/>
                    <blockquote class="mt-3 flex-1 text-ink-soft">{{ $review->quote }}</blockquote>
                    <figcaption class="mt-5 border-t border-brand-100 pt-4 text-sm">
                        <span class="font-semibold text-brand-800">{{ $review->name }}</span>@if ($review->location), <span class="text-ink-soft">{{ $review->location }}</span>@endif
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 9. FAQ --}}
@if ($faqs->isNotEmpty())
<section class="section-y bg-sand/60" aria-labelledby="faq-title" id="faq">
    <div class="container-x grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
        <x-section-heading align="left" :eyebrow="$t('faq_eyebrow')" :title="$t('faq_title')" class="lg:sticky lg:top-28 lg:self-start" id="faq-title"/>
        <div>@foreach ($faqs as $faq)<x-faq-item :question="$faq->question">{{ $faq->answer }}</x-faq-item>@endforeach</div>
    </div>
</section>
@endif

{{-- 10. IMAGINE YOUR JOURNEY --}}
<section class="relative isolate overflow-hidden bg-brand-900 py-24 text-white sm:py-32" aria-labelledby="journey-imagine-title">
    <video class="hero-video absolute inset-0 -z-20 h-full w-full object-cover" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80">
        <source src="https://player.vimeo.com/external/449928180.sd.mp4?s=6d4c4d7e3f95a5a1d5f0b11b6d30f4d2d8d7d2a0&profile_id=164&oauth2_token_id=57447761" type="video/mp4">
    </video>
    <div class="hero-scrim absolute inset-0 -z-10" aria-hidden="true"></div>

    <div class="container-x">
        <div class="max-w-3xl py-8 md:py-12">
            <p class="text-[0.7rem] font-semibold uppercase tracking-[0.2em] text-gold-300">Picture this</p>
            <h3 id="journey-imagine-title" class="mt-3 text-4xl leading-tight text-white sm:text-5xl lg:text-6xl">Picture yourself by an Alpine lake.</h3>
            <p class="mt-4 max-w-xl text-lg leading-relaxed text-white/80">Tell us what you imagine and we will shape the journey around it.</p>
        </div>
    </div>
</section>

{{-- 11. DISCOVER AUSTRIA --}}
<section class="section-y bg-sand/60" aria-labelledby="discover-austria-title">
    <div class="container-x">
        <div class="mb-8 max-w-4xl">
            <p class="eyebrow">DISCOVER AUSTRIA</p>
            <h2 id="discover-austria-title" class="mt-3 text-4xl leading-tight text-brand-800 sm:text-5xl">Planning Your Next <em class="font-medium italic text-gold-600">Austrian Escape?</em></h2>
            <p class="mt-4 max-w-3xl text-lg text-ink-soft">From Austria’s majestic Alpine landscapes to its charming villages and elegant historic cities, discover remarkable destinations, authentic experiences, and unforgettable journeys—thoughtfully tailored around the way you want to explore Austria.</p>
        </div>

        <div class="mb-8">
            <x-button size="lg" x-data @click="$dispatch('open-enquiry', { form_name: 'Discover Austria CTA' })">Get a free quote</x-button>
        </div>

        @php
            $packagesCarousel = $featured->take(6);
        @endphp

        @if ($packagesCarousel->isNotEmpty())
            <div class="overflow-x-auto pb-2">
                <div class="flex gap-6 min-w-max">
                    @foreach ($packagesCarousel as $package)
                        <div class="w-[22rem] shrink-0">
                            <x-package-card :package="$package"/>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

{{-- 11. BLOG --}}
<section class="section-y" aria-labelledby="blog-title">
    <div class="container-x">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Blog</p>
                <h2 id="blog-title" class="mt-2 text-4xl leading-tight text-brand-800 sm:text-5xl">Latest from Austria</h2>
            </div>
            <a href="{{ route('packages.index') }}" class="hidden text-sm font-semibold text-brand-700 hover:text-gold-700 sm:inline-flex">View all articles <x-icon name="arrow-right" class="h-4 w-4"/></a>
        </div>

        @php
            $blogPosts = [
                [
                    'title' => 'The most scenic alpine roads in Austria',
                    'excerpt' => 'From dramatic passes to lakeside drives, these routes reveal the very best of Austrian scenery.',
                    'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80',
                    'meta' => 'Travel Guide',
                ],
                [
                    'title' => 'A weekend in Vienna, Salzburg and the Wachau',
                    'excerpt' => 'A refined itinerary balancing imperial history, riverside wine villages and long lunches.',
                    'image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=1200&q=80',
                    'meta' => 'Itineraries',
                ],
                [
                    'title' => 'What to know before exploring the Austrian Alps',
                    'excerpt' => 'Practical tips for rail routes, mountain villages and choosing the right pace for your holiday.',
                    'image' => 'https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=1200&q=80',
                    'meta' => 'Planning',
                ],
            ];
        @endphp

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($blogPosts as $post)
                <article class="overflow-hidden rounded-[var(--radius-card)] bg-white shadow-card">
                    <div class="aspect-[16/11] bg-cover bg-center" style="background-image: url('{{ $post['image'] }}');"></div>
                    <div class="p-6">
                        <p class="text-[0.7rem] font-semibold uppercase tracking-[0.18em] text-gold-700">{{ $post['meta'] }}</p>
                        <h3 class="mt-3 text-2xl leading-tight text-brand-800">{{ $post['title'] }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-ink-soft">{{ $post['excerpt'] }}</p>
                        <a href="{{ route('packages.index') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-brand-700 hover:text-gold-700">Read article <x-icon name="arrow-right" class="h-4 w-4"/></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 12. FINAL CALL TO ACTION --}}
<section class="section-y bg-brand-700 text-center text-white" aria-labelledby="cta-title">
    <div class="container-x max-w-3xl">
        <h2 id="cta-title" class="text-4xl !text-white sm:text-5xl">{{ $t('cta_title') }}</h2>
        <p class="mt-4 text-lg text-white/85">{{ $t('cta_text') }}</p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <x-button size="lg" x-data @click="$dispatch('open-enquiry', { form_name: 'Final CTA Button' })">Get my free quote</x-button>
            @if (setting('phone'))<x-button size="lg" variant="outline-light" :href="'tel:'.preg_replace('/[^\d+]/', '', setting('phone'))" icon="phone">Call {{ setting('phone') }}</x-button>@endif
        </div>
    </div>
</section>
@endsection

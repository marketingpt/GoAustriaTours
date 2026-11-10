@php
    $phone = setting('phone'); $email = setting('email'); $address = setting('address');
    $socials = array_filter(['Facebook' => setting('social_facebook'), 'Instagram' => setting('social_instagram'), 'YouTube' => setting('social_youtube'), 'TikTok' => setting('social_tiktok')]);
@endphp
<footer class="bg-brand-900 text-white/80">
    <div class="container-x grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <p class="font-display text-3xl font-semibold text-white">{{ setting('brand_name') }}</p>
            <p class="mt-3 max-w-xs text-sm">{{ setting('footer_text') }}</p>
        </div>
        <nav aria-label="Explore">
            <p class="eyebrow !text-gold-300 mb-3">Explore</p>
            <ul class="space-y-2 text-sm">
                <li><a class="hover:text-white" href="{{ route('packages.index') }}">Journeys</a></li>
                @foreach ($navTourTypes as $t)<li><a class="hover:text-white" href="{{ route('packages.index', ['type' => $t->slug]) }}">{{ $t->name }}</a></li>@endforeach
                <li><a class="hover:text-white" href="{{ route('about') }}">About us</a></li>
                <li><a class="hover:text-white" href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </nav>
        <nav aria-label="Destinations">
            <p class="eyebrow !text-gold-300 mb-3">Destinations</p>
            <ul class="space-y-2 text-sm">@foreach ($navDestinations as $d)<li><a class="hover:text-white" href="{{ route('destinations.show', $d->slug) }}">{{ $d->name }}</a></li>@endforeach</ul>
        </nav>
        <div>
            <p class="eyebrow !text-gold-300 mb-3">Contact</p>
            <ul class="space-y-2 text-sm">
                @if ($phone)<li><a class="hover:text-white" href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a></li>@endif
                @if ($email)<li><a class="hover:text-white" href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
                @if ($address)<li class="whitespace-pre-line">{{ $address }}</li>@endif
                @unless ($phone || $email || $address)<li><a class="hover:text-white" href="{{ route('contact') }}">Send us a message</a></li>@endunless
            </ul>
            @if ($socials)
                <ul class="mt-4 flex flex-wrap gap-3 text-sm">@foreach ($socials as $name => $url)<li><a class="underline-offset-4 hover:text-white hover:underline" rel="noopener" target="_blank" href="{{ $url }}">{{ $name }}</a></li>@endforeach</ul>
            @endif
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="container-x flex flex-col gap-2 py-5 text-xs text-white/60 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ setting('legal_name') }}. All rights reserved.</p>
            <ul class="flex gap-5">
                <li><a class="hover:text-white" href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                <li><a class="hover:text-white" href="{{ route('privacy') }}">Privacy Policy</a></li>
            </ul>
        </div>
    </div>
</footer>

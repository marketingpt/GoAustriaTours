@props(['package', 'heading' => 'h3'])
@php
    $modal = ['form_name' => 'Package Quote', 'heading' => 'Get a quote: '.$package->title, 'package_id' => $package->id, 'package_name' => $package->title, 'tour_type' => $package->tourType?->name];
@endphp
<article class="group flex h-full flex-col overflow-hidden rounded-[var(--radius-card)] bg-white shadow-card transition duration-300 hover:-translate-y-1 hover:shadow-lift">
    <a href="{{ route('packages.show', $package->slug) }}" class="relative block overflow-hidden" tabindex="-1" aria-hidden="true">
        <x-picture :media="$package->media" sizes="(min-width:1024px) 400px, (min-width:640px) 50vw, 100vw" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105" :width="640"/>
        @if ($package->tourType)
            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-700">{{ $package->tourType->name }}</span>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-6">
        <p class="flex flex-wrap items-center gap-x-3 text-xs font-semibold uppercase tracking-widest text-gold-700">
            <span>{{ $package->nights }} nights</span>
            @if ($package->destination)<span aria-hidden="true">·</span><span>{{ $package->destination->name }}</span>@endif
            @if ($package->is_sample)<span class="rounded bg-gold-100 px-2 py-0.5 text-[0.65rem] text-gold-800">Sample</span>@endif
        </p>
        <{{ $heading }} class="mt-2 text-2xl"><a href="{{ route('packages.show', $package->slug) }}" class="hover:text-gold-700">{{ $package->title }}</a></{{ $heading }}>
        <p class="mt-2 flex-1 text-sm text-ink-soft">{{ $package->summary }}</p>
        <div class="mt-5 flex items-end justify-between gap-3 border-t border-brand-100 pt-4">
            <div>
                @if ($package->priceLabel())
                    <p class="text-xs text-ink-soft">From</p>
                    <p class="font-display text-3xl font-semibold text-brand-800">{{ $package->priceLabel() }}<span class="ml-1 text-xs font-sans font-normal text-ink-soft">pp</span></p>
                @else
                    <p class="text-sm font-medium text-ink-soft">Price on request</p>
                @endif
            </div>
            <x-button size="sm" x-data @click="$dispatch('open-enquiry', {{ Js::from($modal) }})">Get a Quote</x-button>
        </div>
    </div>
</article>

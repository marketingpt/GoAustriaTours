@props(['question'])
{{-- Native <details>: accessible, keyboard friendly, works without JavaScript. --}}
<details {{ $attributes->class(['group border-b border-brand-100 py-1']) }}>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-4 text-left font-display text-xl font-semibold text-brand-800 marker:content-none [&::-webkit-details-marker]:hidden">
        <span>{{ $question }}</span>
        <x-icon name="chevron-down" class="h-5 w-5 shrink-0 text-gold-600 transition group-open:rotate-180"/>
    </summary>
    <div class="pb-5 pr-8 text-ink-soft">{{ $slot }}</div>
</details>

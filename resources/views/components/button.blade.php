@props(['href' => null, 'variant' => 'primary', 'size' => 'md', 'type' => 'button', 'icon' => null])
@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-full font-semibold tracking-wide transition duration-200 focus-visible:outline-offset-4 disabled:cursor-not-allowed disabled:opacity-60';
    $sizes = ['sm' => 'px-4 py-2 text-sm', 'md' => 'px-6 py-3 text-[0.95rem]', 'lg' => 'px-8 py-4 text-base'];
    $variants = [
        'primary' => 'bg-cta-600 text-white shadow-sm hover:bg-cta-700 hover:shadow-md',
        'secondary' => 'bg-brand-700 text-white hover:bg-brand-800',
        'outline' => 'border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white',
        'outline-light' => 'border border-white/70 text-white hover:bg-white hover:text-brand-800',
        'light' => 'bg-white text-brand-800 hover:bg-gold-100',
        'ghost' => 'text-brand-700 hover:bg-brand-50',
    ];
    $classes = $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}@if ($icon)<x-icon :name="$icon" class="h-4 w-4"/>@endif</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}@if ($icon)<x-icon :name="$icon" class="h-4 w-4"/>@endif</button>
@endif

@props(['media' => null, 'sizes' => '100vw', 'eager' => false, 'width' => 960, 'alt' => null, 'label' => 'Image placeholder'])
@if ($media)
    <img
        src="{{ $media->url($width) }}"
        srcset="{{ $media->srcset() }}"
        sizes="{{ $sizes }}"
        width="{{ $media->width }}"
        height="{{ $media->height }}"
        alt="{{ $alt ?? $media->alt }}"
        @if ($eager) fetchpriority="high" decoding="async" @else loading="lazy" decoding="async" @endif
        {{ $attributes }}
    >
@else
    {{-- Clearly labelled placeholder: upload a real image in Admin > Media --}}
    <div {{ $attributes->class(['flex items-center justify-center bg-brand-100 text-sm font-medium text-brand-600']) }} role="img" aria-label="{{ $label }}">{{ $label }}</div>
@endif

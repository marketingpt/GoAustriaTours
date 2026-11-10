@props(['eyebrow' => null, 'title', 'text' => null, 'align' => 'center', 'tag' => 'h2', 'light' => false])
<div {{ $attributes->class(['max-w-3xl', 'mx-auto text-center' => $align === 'center', 'mb-10 sm:mb-14']) }}>
    @if ($eyebrow)
        <p class="eyebrow mb-3 {{ $light ? '!text-gold-300' : '' }}">{{ $eyebrow }}</p>
    @endif
    <{{ $tag }} class="text-4xl sm:text-5xl {{ $light ? '!text-white' : '' }}">{{ $title }}</{{ $tag }}>
    <span class="mt-5 block h-px w-16 bg-gold-500 {{ $align === 'center' ? 'mx-auto' : '' }}" aria-hidden="true"></span>
    @if ($text)
        <p class="mt-5 text-lg {{ $light ? 'text-white/85' : 'text-ink-soft' }}">{{ $text }}</p>
    @endif
</div>

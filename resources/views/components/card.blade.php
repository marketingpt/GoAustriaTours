@props(['padded' => true])
<div {{ $attributes->class(['overflow-hidden rounded-[var(--radius-card)] bg-white shadow-card', 'p-6 sm:p-8' => $padded]) }}>
    {{ $slot }}
</div>

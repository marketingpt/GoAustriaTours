@props(['name', 'label', 'id', 'required' => false, 'hint' => null, 'error' => ''])
{{-- Label + control (slot) + hint + inline error. Errors come from the server (no-JS) or Alpine `errors` (AJAX). --}}
<div {{ $attributes }}>
    <label for="{{ $id }}" class="mb-1 block text-sm font-medium text-ink">{{ $label }}@if ($required)<span class="text-cta-700" aria-hidden="true"> *</span>@endif</label>
    {{ $slot }}
    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1 text-xs text-ink-soft">{{ $hint }}</p>
    @endif
    @if ($error)
        <p id="{{ $id }}-err" class="mt-1 text-sm font-medium text-red-700" role="alert">{{ $error }}</p>
    @endif
    <p x-cloak x-show="errors['{{ $name }}']" x-text="(errors['{{ $name }}'] || [])[0]" class="mt-1 text-sm font-medium text-red-700" role="alert"></p>
</div>

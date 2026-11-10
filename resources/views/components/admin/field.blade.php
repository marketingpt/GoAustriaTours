@props(['name', 'label', 'hint' => null, 'for' => null])
<div {{ $attributes }}>
    <label @if ($for ?? $name) for="{{ $for ?? $name }}" @endif class="mb-1 block text-sm font-medium text-ink">{{ $label }}</label>
    {{ $slot }}
    @if ($hint)<p class="mt-1 text-xs text-ink-soft">{{ $hint }}</p>@endif
    @php $errKey = \Illuminate\Support\Str::of($name)->replace(['[', ']'], ['.', ''])->toString(); @endphp
    @error($errKey)<p class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
</div>

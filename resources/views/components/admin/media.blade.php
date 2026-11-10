@props(['name', 'label', 'selected' => null, 'multiple' => false, 'hint' => null])
@php
    static $library = null;
    $library ??= \App\Models\Media::latest()->limit(400)->get()->map(fn ($m) => ['id' => $m->id, 'url' => $m->url(480), 'alt' => $m->alt])->all();
    $ids = collect($multiple ? (array) old($name, $selected ?? []) : [old($name, $selected)])->filter()->map(fn ($v) => (int) $v)->values()->all();
@endphp
<div x-data="mediaPicker({ multiple: {{ $multiple ? 'true' : 'false' }}, items: {{ Js::from($library) }}, selected: {{ Js::from($ids) }} })" {{ $attributes }}>
    <p class="mb-1 text-sm font-medium">{{ $label }}</p>
    <template x-for="id in selected" :key="id"><input type="hidden" name="{{ $name }}{{ $multiple ? '[]' : '' }}" :value="id"></template>
    @if (! $multiple)<input type="hidden" name="{{ $name }}" value="" x-bind:disabled="selected.length > 0">@endif

    <ul class="mb-2 flex flex-wrap gap-2" x-show="chosen.length">
        <template x-for="(m, i) in chosen" :key="m.id">
            <li class="w-28 rounded-lg border border-brand-200 bg-white p-1 text-center text-xs">
                <img :src="m.url" :alt="m.alt" class="h-20 w-full rounded object-cover">
                <div class="mt-1 flex justify-center gap-1">
                    <template x-if="multiple"><span class="flex gap-1"><button type="button" @click="move(i, -1)" class="rounded px-1 hover:bg-brand-50" aria-label="Move earlier">&larr;</button><button type="button" @click="move(i, 1)" class="rounded px-1 hover:bg-brand-50" aria-label="Move later">&rarr;</button></span></template>
                    <button type="button" @click="remove(m.id)" class="rounded px-1 text-red-700 hover:bg-red-50">Remove</button>
                </div>
            </li>
        </template>
    </ul>
    <button type="button" @click="open = !open" class="rounded-lg border border-brand-300 bg-white px-3 py-1.5 text-sm font-medium hover:bg-brand-50" x-text="open ? 'Close library' : '{{ $multiple ? 'Choose images' : 'Choose image' }}'"></button>
    <div x-show="open" x-cloak class="mt-2 max-h-72 overflow-y-auto rounded-lg border border-brand-200 bg-white p-2">
        <p x-show="!items.length" class="p-2 text-sm text-ink-soft">No images yet. Upload some in Media first.</p>
        <ul class="grid grid-cols-3 gap-2 sm:grid-cols-5">
            <template x-for="m in items" :key="m.id">
                <li><button type="button" @click="toggle(m.id)" class="block w-full overflow-hidden rounded border-2" :class="selected.includes(m.id) ? 'border-gold-500' : 'border-transparent'" :title="m.alt"><img :src="m.url" :alt="m.alt" loading="lazy" class="h-20 w-full object-cover"></button></li>
            </template>
        </ul>
    </div>
    @if ($hint)<p class="mt-1 text-xs text-ink-soft">{{ $hint }}</p>@endif
</div>

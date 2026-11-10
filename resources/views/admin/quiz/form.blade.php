@if (! $item->exists)<p class="rounded-lg bg-gold-50 p-3 text-sm text-gold-800">Tip: the quiz always ends with the contact form. Questions run in the order shown on the Quiz list.</p>@endif
<x-admin.input name="question" label="Question shown to visitors" :value="$item->question" required/>
<div class="grid gap-5 sm:grid-cols-3">
    <x-admin.input name="label" label="Short label" :value="$item->label" required hint="Used in emails/CRM, e.g. 'Time of Year'. If you rename it after launch, rename the matching GoHighLevel field too."/>
    <x-admin.select name="type" label="Answer style" :options="['tiles' => 'Tiles (click to advance)', 'select' => 'Dropdown (with Next button)']" :value="$item->type ?? 'tiles'"/>
    @if ($item->exists)
        <div><p class="mb-1 text-sm font-medium">Answer key</p><p class="input !bg-brand-50">{{ $item->field_key }}</p><input type="hidden" name="field_key" value="{{ $item->field_key }}"></div>
    @else
        <x-admin.input name="field_key" label="Answer key" :value="null" hint="Optional. Letters, numbers and underscores. Cannot be changed later."/>
    @endif
</div>

<fieldset class="space-y-3 rounded-lg border border-brand-100 p-4" x-data="rows({{ Js::from(old('options', $options)) }}, { label: '', icon: '', is_active: true })">
    <legend class="px-2 text-sm font-semibold">Answer options (in display order)</legend>
    <template x-for="(row, i) in rows" :key="i">
        <div class="grid items-center gap-2 rounded-lg bg-brand-50/60 p-3 sm:grid-cols-[2fr_1fr_auto_auto]">
            <input class="input" :name="`options[${i}][label]`" x-model="row.label" placeholder="Answer text" aria-label="Answer text">
            <select class="input" :name="`options[${i}][icon]`" x-model="row.icon" aria-label="Icon">
                <option value="">No icon</option>
                @foreach ($icons as $ic)<option value="{{ $ic }}">{{ $ic }}</option>@endforeach
            </select>
            <label class="flex items-center gap-1 text-sm"><input type="hidden" :name="`options[${i}][is_active]`" value="0"><input type="checkbox" :name="`options[${i}][is_active]`" value="1" x-model="row.is_active" class="accent-brand-700"> Active</label>
            <div class="flex gap-1"><button type="button" @click="move(i, -1)" class="rounded px-2 py-1 hover:bg-white" aria-label="Move up">&uarr;</button><button type="button" @click="move(i, 1)" class="rounded px-2 py-1 hover:bg-white" aria-label="Move down">&darr;</button><button type="button" @click="remove(i)" class="rounded px-2 py-1 text-red-700 hover:bg-white">Remove</button></div>
        </div>
    </template>
    <button type="button" @click="add()" class="rounded-lg border border-brand-300 px-3 py-1.5 text-sm font-medium hover:bg-brand-50">+ Add option</button>
</fieldset>

<x-admin.checkbox name="is_active" label="Active (shown in the quiz)" :checked="$item->exists ? $item->is_active : true"/>

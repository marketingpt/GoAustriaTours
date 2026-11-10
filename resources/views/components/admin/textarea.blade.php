@props(['name', 'label', 'value' => null, 'hint' => null, 'rows' => 3])
<x-admin.field :name="$name" :label="$label" :hint="$hint" {{ $attributes->only('class') }}>
    <textarea class="input" id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->except('class') }}>{{ old(\Illuminate\Support\Str::of($name)->replace(['[', ']'], ['.', ''])->toString(), $value) }}</textarea>
</x-admin.field>

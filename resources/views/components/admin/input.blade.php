@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])
<x-admin.field :name="$name" :label="$label" :hint="$hint" {{ $attributes->only('class') }}>
    <input class="input" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $type === 'password' ? '' : old(\Illuminate\Support\Str::of($name)->replace(['[', ']'], ['.', ''])->toString(), $value) }}" @required($required) {{ $attributes->except('class') }}>
</x-admin.field>

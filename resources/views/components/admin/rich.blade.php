@props(['name', 'label', 'value' => null, 'hint' => null])
<x-admin.field :name="$name" :label="$label" :hint="$hint" :for="$name.'-editor'">
    <input id="{{ $name }}" type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">
    <trix-editor id="{{ $name }}-editor" input="{{ $name }}" class="prose-content !max-w-none"></trix-editor>
</x-admin.field>

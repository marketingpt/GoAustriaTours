@props(['name', 'label', 'options' => [], 'value' => null, 'hint' => null, 'blank' => null])
<x-admin.field :name="$name" :label="$label" :hint="$hint" {{ $attributes->only('class') }}>
    <select class="input" id="{{ $name }}" name="{{ $name }}" {{ $attributes->except('class') }}>
        @if ($blank !== null)<option value="">{{ $blank }}</option>@endif
        @foreach ($options as $k => $v)<option value="{{ $k }}" @selected((string) old($name, $value) === (string) $k)>{{ $v }}</option>@endforeach
    </select>
</x-admin.field>

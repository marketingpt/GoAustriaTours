@props(['name', 'label', 'checked' => false])
<label class="flex items-center gap-2 text-sm font-medium">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" class="h-4 w-4 accent-brand-700" @checked(old($name, $checked))>
    {{ $label }}
</label>

@php $icons = ['car', 'user', 'bus', 'compass', 'mountain', 'map', 'sparkles', 'heart', 'sun']; @endphp
<div class="grid gap-5 sm:grid-cols-2">
    <x-admin.input name="name" label="Name" :value="$item->name" required/>
    <x-admin.input name="slug" label="URL slug" :value="$item->slug" hint="Leave empty to generate from the name."/>
</div>
<x-admin.input name="tagline" label="Tagline" :value="$item->tagline"/>
<x-admin.textarea name="description" label="Short description" :value="$item->description"/>
<x-admin.select name="icon" label="Icon" :options="array_combine($icons, $icons)" :value="$item->icon" blank="None"/>
<x-admin.media name="media_id" label="Card image" :selected="$item->media_id"/>
<x-admin.checkbox name="is_active" label="Active (shown on the website)" :checked="$item->exists ? $item->is_active : true"/>

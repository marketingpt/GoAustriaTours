<div class="grid gap-5 sm:grid-cols-2">
    <x-admin.input name="name" label="Guest name" :value="$item->name" required/>
    <x-admin.input name="location" label="Location" :value="$item->location" hint="e.g. Boston, MA"/>
</div>
<x-admin.textarea name="quote" label="Review" :value="$item->quote" rows="5" required/>
<x-admin.checkbox name="is_placeholder" label="This is placeholder text (shows a 'Placeholder' tag on the website)" :checked="$item->exists ? $item->is_placeholder : false"/>
<x-admin.checkbox name="is_active" label="Active (shown on the website)" :checked="$item->exists ? $item->is_active : true"/>

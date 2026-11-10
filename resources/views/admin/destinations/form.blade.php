<div class="grid gap-5 sm:grid-cols-2">
    <x-admin.input name="name" label="Name" :value="$item->name" required/>
    <x-admin.input name="slug" label="URL slug" :value="$item->slug" hint="Leave empty to generate from the name."/>
</div>
<x-admin.input name="tagline" label="Tagline" :value="$item->tagline"/>
<x-admin.textarea name="intro" label="Intro (short)" :value="$item->intro"/>
<x-admin.rich name="body" label="Page text" :value="$item->body"/>
<x-admin.media name="media_id" label="Hero image" :selected="$item->media_id"/>
<fieldset class="space-y-4 rounded-lg border border-brand-100 p-4">
    <legend class="px-2 text-sm font-semibold">Search engines</legend>
    <x-admin.input name="seo_title" label="SEO title" :value="$item->seo_title" hint="About 50-60 characters."/>
    <x-admin.textarea name="meta_description" label="Meta description" :value="$item->meta_description" hint="About 150 characters."/>
    <x-admin.checkbox name="noindex" label="Hide from search engines (noindex)" :checked="$item->noindex"/>
</fieldset>
<x-admin.checkbox name="is_active" label="Active (shown on the website)" :checked="$item->exists ? $item->is_active : true"/>

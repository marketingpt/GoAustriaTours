<div class="grid gap-5 sm:grid-cols-2">
    <x-admin.input name="title" label="Title" :value="$item->title" required/>
    <x-admin.input name="slug" label="URL slug" :value="$item->slug" hint="Leave empty to generate from the title."/>
    <x-admin.select name="tour_type_id" label="Tour type" :options="$tourTypes" :value="$item->tour_type_id" blank="None"/>
    <x-admin.select name="destination_id" label="Destination" :options="$destinations" :value="$item->destination_id" blank="None"/>
    <x-admin.input name="nights" label="Nights" type="number" :value="$item->nights ?? 7" required min="1"/>
    <div class="grid grid-cols-[1fr_6rem] gap-3">
        <x-admin.input name="price_from" label="'From' price (per person)" type="number" step="0.01" :value="$item->price_from" hint="Leave empty for 'price on request'."/>
        <x-admin.input name="currency" label="Currency" :value="$item->currency ?? 'USD'" maxlength="3" required/>
    </div>
</div>
<x-admin.input name="price_note" label="Price note" :value="$item->price_note" hint="Small print under the price, e.g. 'per person sharing'."/>
<x-admin.textarea name="summary" label="Short summary" :value="$item->summary" hint="Shown on cards and as the page sub-heading."/>
<x-admin.rich name="description" label="Full description" :value="$item->description"/>

<div class="grid gap-5 sm:grid-cols-3">
    <x-admin.textarea name="highlights" label="Highlights" :value="old('highlights', implode(PHP_EOL, $item->highlights ?? []))" rows="6" hint="One per line."/>
    <x-admin.textarea name="inclusions" label="Included" :value="old('inclusions', implode(PHP_EOL, $item->inclusions ?? []))" rows="6" hint="One per line."/>
    <x-admin.textarea name="exclusions" label="Not included" :value="old('exclusions', implode(PHP_EOL, $item->exclusions ?? []))" rows="6" hint="One per line."/>
</div>

<fieldset class="space-y-3 rounded-lg border border-brand-100 p-4" x-data="rows({{ Js::from(old('itinerary', $item->itinerary ?? [])) }}, { title: '', body: '' })">
    <legend class="px-2 text-sm font-semibold">Day-by-day itinerary</legend>
    <template x-for="(row, i) in rows" :key="i">
        <div class="grid gap-2 rounded-lg bg-brand-50/60 p-3 sm:grid-cols-[1fr_2fr_auto]">
            <input class="input" :name="`itinerary[${i}][title]`" x-model="row.title" placeholder="Day 1-2: Vienna" aria-label="Day title">
            <textarea class="input" rows="2" :name="`itinerary[${i}][body]`" x-model="row.body" placeholder="What happens" aria-label="Day description"></textarea>
            <div class="flex items-start gap-1"><button type="button" @click="move(i, -1)" class="rounded px-2 py-1 hover:bg-white" aria-label="Move up">&uarr;</button><button type="button" @click="move(i, 1)" class="rounded px-2 py-1 hover:bg-white" aria-label="Move down">&darr;</button><button type="button" @click="remove(i)" class="rounded px-2 py-1 text-red-700 hover:bg-white">Remove</button></div>
        </div>
    </template>
    <button type="button" @click="add()" class="rounded-lg border border-brand-300 px-3 py-1.5 text-sm font-medium hover:bg-brand-50">+ Add day</button>
</fieldset>

<fieldset class="space-y-5 rounded-lg border border-brand-100 p-4">
    <legend class="px-2 text-sm font-semibold">Images</legend>
    <x-admin.media name="media_id" label="Main image (card and top of page)" :selected="$item->media_id" hint="Images need alt text: set it in Media."/>
    <x-admin.media name="gallery" label="Gallery (in display order)" :selected="$galleryIds" multiple/>
</fieldset>

<fieldset class="space-y-3 rounded-lg border border-brand-100 p-4" x-data="rows({{ Js::from(old('faqs', $item->faqs ?? [])) }}, { question: '', answer: '' })">
    <legend class="px-2 text-sm font-semibold">Package FAQs (also sent to Google as FAQ data)</legend>
    <template x-for="(row, i) in rows" :key="i">
        <div class="grid gap-2 rounded-lg bg-brand-50/60 p-3 sm:grid-cols-[1fr_2fr_auto]">
            <input class="input" :name="`faqs[${i}][question]`" x-model="row.question" placeholder="Question" aria-label="Question">
            <textarea class="input" rows="2" :name="`faqs[${i}][answer]`" x-model="row.answer" placeholder="Answer" aria-label="Answer"></textarea>
            <button type="button" @click="remove(i)" class="self-start rounded px-2 py-1 text-red-700 hover:bg-white">Remove</button>
        </div>
    </template>
    <button type="button" @click="add()" class="rounded-lg border border-brand-300 px-3 py-1.5 text-sm font-medium hover:bg-brand-50">+ Add FAQ</button>
</fieldset>

<fieldset class="space-y-4 rounded-lg border border-brand-100 p-4">
    <legend class="px-2 text-sm font-semibold">Search engines and sharing</legend>
    <x-admin.input name="seo_title" label="SEO title" :value="$item->seo_title" hint="About 50-60 characters. Empty = the package title."/>
    <x-admin.textarea name="meta_description" label="Meta description" :value="$item->meta_description" hint="About 150 characters."/>
    <x-admin.media name="og_media_id" label="Social share image" :selected="$item->og_media_id" hint="Empty = main image."/>
    <x-admin.checkbox name="noindex" label="Hide from search engines (noindex)" :checked="$item->noindex"/>
</fieldset>

<div class="flex flex-wrap gap-x-8 gap-y-3">
    <x-admin.checkbox name="is_published" label="Published" :checked="$item->exists ? $item->is_published : false"/>
    <x-admin.checkbox name="is_featured" label="Featured on the home page" :checked="$item->is_featured"/>
    <x-admin.checkbox name="is_sample" label="Sample / placeholder content (shows a 'Sample' tag)" :checked="$item->exists ? $item->is_sample : false"/>
</div>

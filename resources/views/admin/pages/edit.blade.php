@extends('layouts.admin', ['title' => 'Edit '.$page->title])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-4xl">Edit: {{ $page->title }}</h1>
    <a href="{{ route('admin.pages.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">&larr; All pages</a>
</div>

<form method="POST" action="{{ route('admin.pages.update', $page->slug) }}" class="space-y-6">
    @csrf @method('PUT')

    <div class="space-y-5 rounded-xl bg-white p-5 shadow-card sm:p-8">
        <x-admin.input name="title" label="Page title" :value="$page->title" required/>

        @if (in_array($page->slug, ['about', 'terms', 'privacy']))
            @if ($page->slug !== 'about')<p class="rounded-lg bg-gold-50 p-3 text-sm text-gold-800">Legal pages are starter templates. Have them reviewed by a lawyer. Use <code>{company}</code> to insert the legal company name from Settings.</p>@endif
            <x-admin.rich name="body" label="Page text" :value="$page->body"/>
        @elseif ($page->slug === 'contact')
            <x-admin.textarea name="content[intro]" label="Intro text" :value="$page->text('intro')"/>
        @endif
    </div>

    @foreach ($homeFields as $group => $fields)
        <fieldset class="space-y-5 rounded-xl bg-white p-5 shadow-card sm:p-8">
            <legend class="float-left mb-2 w-full font-display text-2xl text-brand-800">{{ $group }}</legend>
            <div class="clear-both"></div>
            @foreach ($fields as $key => [$label, $default, $type])
                @if ($type === 'media')
                    <x-admin.media :name="'content['.$key.']'" :label="$label" :selected="$page->content[$key] ?? null"/>
                @elseif ($type === 'textarea')
                    <x-admin.textarea :name="'content['.$key.']'" :label="$label" :value="$page->content[$key] ?? $default" rows="3"/>
                @else
                    <x-admin.input :name="'content['.$key.']'" :label="$label" :value="$page->content[$key] ?? $default"/>
                @endif
            @endforeach
        </fieldset>
    @endforeach

    <fieldset class="space-y-5 rounded-xl bg-white p-5 shadow-card sm:p-8">
        <legend class="float-left mb-2 w-full font-display text-2xl text-brand-800">Search engines and sharing</legend>
        <div class="clear-both"></div>
        <x-admin.input name="seo_title" label="SEO title" :value="$page->seo_title" hint="About 50-60 characters."/>
        <x-admin.textarea name="meta_description" label="Meta description" :value="$page->meta_description" hint="About 150 characters."/>
        <x-admin.media name="og_media_id" label="Social share image" :selected="$page->og_media_id"/>
        <x-admin.checkbox name="noindex" label="Hide from search engines (noindex)" :checked="$page->noindex"/>
    </fieldset>

    <button class="rounded-full bg-cta-600 px-8 py-3 font-semibold text-white hover:bg-cta-700">Save page</button>
</form>
@endsection

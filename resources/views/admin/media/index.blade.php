@extends('layouts.admin', ['title' => 'Media'])

@section('content')
<h1 class="mb-6 text-4xl">Media library</h1>

<form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mb-8 grid gap-4 rounded-xl bg-white p-5 shadow-card sm:grid-cols-2 sm:p-6">
    @csrf
    <h2 class="text-2xl sm:col-span-2">Upload an image</h2>
    <x-admin.field name="file" label="Image file (JPG, PNG or WebP, up to 8 MB)" class="sm:col-span-2"><input type="file" id="file" name="file" accept="image/jpeg,image/png,image/webp" required class="input"></x-admin.field>
    <x-admin.input name="alt" label="Alt text (required)" hint="Describe what is in the picture, e.g. 'Rooftops of Hallstatt beside a misty lake'." required/>
    <x-admin.input name="credit" label="Credit / source (optional)" hint="Photographer and licence, e.g. 'Pexels / Name'."/>
    <div class="sm:col-span-2"><button class="rounded-full bg-cta-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-cta-700">Upload</button><p class="mt-2 text-xs text-ink-soft">Use only images you own or that are free for commercial use. Images are resized and converted to WebP automatically.</p></div>
</form>

<ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($items as $m)
        <li class="overflow-hidden rounded-xl bg-white shadow-card">
            <img src="{{ $m->url(480) }}" alt="{{ $m->alt }}" width="{{ $m->width }}" height="{{ $m->height }}" loading="lazy" class="h-44 w-full object-cover">
            <div class="space-y-2 p-4">
                <form method="POST" action="{{ route('admin.media.update', $m) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <label class="block text-xs font-semibold uppercase tracking-wider text-ink-soft" for="alt-{{ $m->id }}">Alt text</label>
                    <input id="alt-{{ $m->id }}" name="alt" value="{{ $m->alt }}" class="input !py-1.5 text-sm" required>
                    <input name="credit" value="{{ $m->credit }}" class="input !py-1.5 text-sm" placeholder="Credit / source" aria-label="Credit">
                    <button class="rounded-full border border-brand-300 px-3 py-1 text-xs font-semibold hover:bg-brand-50">Save</button>
                </form>
                <form method="POST" action="{{ route('admin.media.destroy', $m) }}" x-data="{ ask: false }" class="text-xs">
                    @csrf @method('DELETE')
                    <button type="button" x-show="!ask" @click="ask = true" class="font-semibold text-red-700 hover:underline">Delete</button>
                    <span x-show="ask" x-cloak>Remove from all pages? <button class="font-semibold text-red-700 underline">Yes, delete</button> <button type="button" @click="ask = false">No</button></span>
                </form>
                <p class="text-xs text-ink-soft">{{ $m->width }}×{{ $m->height }} · {{ $m->original_name }}</p>
            </div>
        </li>
    @empty
        <li class="rounded-xl bg-white p-10 text-center text-ink-soft shadow-card sm:col-span-3">No images yet.</li>
    @endforelse
</ul>
@if ($items->hasPages())
    <nav class="mt-6 flex items-center justify-between text-sm" aria-label="Pagination">
        <span class="text-ink-soft">Page {{ $items->currentPage() }} of {{ $items->lastPage() }}</span>
        <span class="flex gap-2">@if ($items->previousPageUrl())<a class="rounded-full border border-brand-300 bg-white px-4 py-1.5" href="{{ $items->previousPageUrl() }}">&larr; Newer</a>@endif @if ($items->nextPageUrl())<a class="rounded-full border border-brand-300 bg-white px-4 py-1.5" href="{{ $items->nextPageUrl() }}">Older &rarr;</a>@endif</span>
    </nav>
@endif
@endsection

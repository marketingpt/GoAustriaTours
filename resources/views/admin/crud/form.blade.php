@extends('layouts.admin', ['title' => ($item->exists ? 'Edit ' : 'Add ').$singular])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-4xl">{{ $item->exists ? 'Edit' : 'Add' }} {{ $singular }}</h1>
    <a href="{{ route("$route.index") }}" class="text-sm font-semibold text-brand-700 hover:underline">&larr; Back to {{ strtolower($plural) }}</a>
</div>

<form method="POST" action="{{ $item->exists ? route("$route.update", $item) : route("$route.store") }}" class="space-y-6 rounded-xl bg-white p-5 shadow-card sm:p-8">
    @csrf
    @if ($item->exists) @method('PUT') @endif
    @include("admin.$view.form")
    <div class="flex gap-3 border-t border-brand-100 pt-5">
        <button class="rounded-full bg-cta-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-cta-700">Save</button>
        <a href="{{ route("$route.index") }}" class="rounded-full px-6 py-2.5 text-sm font-semibold text-ink-soft hover:bg-brand-50">Cancel</a>
    </div>
</form>
@endsection

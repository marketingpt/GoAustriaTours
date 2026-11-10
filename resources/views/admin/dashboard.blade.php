@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
<h1 class="mb-6 text-4xl">Dashboard</h1>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([['Enquiries this week', $weekCount, null], ['New (not yet contacted)', $newCount, 'admin.enquiries.index'], ['Waiting over 24 hours', $unactioned, 'admin.enquiries.index'], ['Published packages', $packageCount, 'admin.packages.index']] as [$label, $n, $route])
        <div class="rounded-xl bg-white p-5 shadow-card">
            <p class="text-sm text-ink-soft">{{ $label }}</p>
            <p class="mt-1 font-display text-5xl text-brand-800">{{ $n }}</p>
            @if ($route)<a href="{{ $label === 'New (not yet contacted)' ? route($route, ['status' => 'new']) : route($route) }}" class="mt-2 inline-block text-sm font-semibold text-brand-700 hover:underline">View</a>@endif
        </div>
    @endforeach
</div>

@if ($ghlFailed)
    <p class="mt-6 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800" role="alert">{{ $ghlFailed }} enquiries failed to sync to GoHighLevel. <a class="font-semibold underline" href="{{ route('admin.enquiries.index', ['ghl' => 'failed']) }}">Review and retry</a>.</p>
@endif
@unless ($ghlOn)
    <p class="mt-6 rounded-lg border border-gold-300 bg-gold-50 p-3 text-sm text-gold-800">GoHighLevel sync is <strong>off</strong> (no token/location ID in .env). Enquiries are still saved and emailed.</p>
@endunless

<section class="mt-8 rounded-xl bg-white p-5 shadow-card sm:p-6" aria-labelledby="recent">
    <div class="mb-3 flex items-center justify-between"><h2 id="recent" class="text-2xl">Recent enquiries</h2><a href="{{ route('admin.enquiries.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">All enquiries</a></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[32rem] text-left text-sm">
            <thead class="text-xs uppercase tracking-wider text-ink-soft"><tr><th class="py-2 pr-3">When</th><th class="pr-3">Name</th><th class="pr-3">Form</th><th class="pr-3">Status</th><th></th></tr></thead>
            <tbody class="divide-y divide-brand-100">
                @forelse ($recent as $e)
                    <tr><td class="py-2 pr-3 whitespace-nowrap">{{ $e->created_at->diffForHumans() }}</td><td class="pr-3">{{ $e->fullName() }}</td><td class="pr-3">{{ $e->form_name }}</td><td class="pr-3">{{ \App\Models\Enquiry::STATUSES[$e->status] ?? $e->status }}</td><td><a class="font-semibold text-brand-700 hover:underline" href="{{ route('admin.enquiries.show', $e) }}">Open</a></td></tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-ink-soft">No enquiries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="mt-8" aria-label="Quick links">
    <h2 class="mb-3 text-2xl">Quick links</h2>
    <div class="flex flex-wrap gap-3">
        @foreach ([['Add a package', route('admin.packages.create')], ['Edit home page text', route('admin.pages.edit', 'home')], ['Edit the quiz', route('admin.quiz.index')], ['Upload images', route('admin.media.index')], ['Site settings', route('admin.settings.edit')]] as [$l, $u])
            <a href="{{ $u }}" class="rounded-full border border-brand-300 bg-white px-4 py-2 text-sm font-medium hover:bg-brand-50">{{ $l }}</a>
        @endforeach
    </div>
</section>
@endsection

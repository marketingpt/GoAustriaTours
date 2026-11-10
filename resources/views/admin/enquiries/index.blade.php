@extends('layouts.admin', ['title' => 'Enquiries'])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-4xl">Enquiries</h1>
    <a href="{{ route('admin.enquiries.export', request()->query()) }}" class="rounded-full border border-brand-300 bg-white px-5 py-2 text-sm font-semibold hover:bg-brand-50">Export CSV{{ request()->query() ? ' (filtered)' : '' }}</a>
</div>

<form method="GET" class="mb-6 grid gap-3 rounded-xl bg-white p-4 shadow-card sm:grid-cols-2 lg:grid-cols-4" aria-label="Filter enquiries">
    <div class="lg:col-span-2"><label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-soft">Search</label><input id="q" name="q" value="{{ request('q') }}" class="input" placeholder="Name, email, phone, package"></div>
    <div><label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-soft">Status</label><select id="status" name="status" class="input"><option value="">All</option>@foreach (\App\Models\Enquiry::STATUSES as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach</select></div>
    <div><label for="source" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-soft">Source</label><select id="source" name="source" class="input"><option value="">All</option>@foreach ($sources as $s)<option @selected(request('source') === $s)>{{ $s }}</option>@endforeach</select></div>
    <div><label for="form" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-soft">Form</label><select id="form" name="form" class="input"><option value="">All</option>@foreach ($forms as $f)<option @selected(request('form') === $f)>{{ $f }}</option>@endforeach</select></div>
    <div><label for="from" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-soft">From</label><input id="from" type="date" name="from" value="{{ request('from') }}" class="input"></div>
    <div><label for="to" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-soft">To</label><input id="to" type="date" name="to" value="{{ request('to') }}" class="input"></div>
    <div class="flex items-end gap-2"><button class="rounded-full bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">Filter</button><a href="{{ route('admin.enquiries.index') }}" class="px-3 py-2.5 text-sm text-ink-soft hover:underline">Reset</a></div>
</form>

<div class="overflow-x-auto rounded-xl bg-white shadow-card">
    <table class="w-full min-w-[48rem] text-left text-sm">
        <thead class="border-b border-brand-100 text-xs uppercase tracking-wider text-ink-soft"><tr><th class="px-4 py-3">Received</th><th>Name</th><th>Contact</th><th>Form / package</th><th>Source</th><th>Status</th><th>CRM</th><th></th></tr></thead>
        <tbody class="divide-y divide-brand-100">
            @forelse ($enquiries as $e)
                <tr @class(['bg-gold-50/50' => $e->status === 'new'])>
                    <td class="whitespace-nowrap px-4 py-3">{{ $e->created_at->format('j M Y H:i') }}</td>
                    <td class="py-3 pr-3 font-medium">{{ $e->fullName() }}</td>
                    <td class="py-3 pr-3"><span class="block">{{ $e->email }}</span><span class="text-ink-soft">{{ $e->phone_e164 }}</span></td>
                    <td class="py-3 pr-3">{{ $e->form_name }}@if ($e->package_name)<span class="block text-ink-soft">{{ $e->package_name }}</span>@endif</td>
                    <td class="py-3 pr-3">{{ $e->source ?? '-' }}</td>
                    <td class="py-3 pr-3">{{ \App\Models\Enquiry::STATUSES[$e->status] ?? $e->status }}</td>
                    <td class="py-3 pr-3">{{ ucfirst($e->ghl_status) }}</td>
                    <td class="py-3 pr-4 text-right"><a href="{{ route('admin.enquiries.show', $e) }}" class="font-semibold text-brand-700 hover:underline">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-ink-soft">No enquiries match.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if ($enquiries->hasPages())
    <nav class="mt-5 flex items-center justify-between text-sm" aria-label="Pagination">
        <span class="text-ink-soft">Page {{ $enquiries->currentPage() }} of {{ $enquiries->lastPage() }} ({{ $enquiries->total() }} total)</span>
        <span class="flex gap-2">
            @if ($enquiries->previousPageUrl())<a class="rounded-full border border-brand-300 bg-white px-4 py-1.5 font-medium hover:bg-brand-50" href="{{ $enquiries->previousPageUrl() }}">&larr; Newer</a>@endif
            @if ($enquiries->nextPageUrl())<a class="rounded-full border border-brand-300 bg-white px-4 py-1.5 font-medium hover:bg-brand-50" href="{{ $enquiries->nextPageUrl() }}">Older &rarr;</a>@endif
        </span>
    </nav>
@endif
@endsection

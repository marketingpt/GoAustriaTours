@extends('layouts.admin', ['title' => 'Enquiry #'.$e->id])

@php
    $rows = fn (array $pairs) => collect($pairs)->filter(fn ($v) => filled($v));
    $dt = fn ($key) => $e->tracking_first[$key] ?? null;
@endphp

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-4xl">{{ $e->fullName() }}</h1>
    <a href="{{ route('admin.enquiries.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">&larr; All enquiries</a>
</div>

<div class="grid gap-6 lg:grid-cols-[1fr_20rem]">
    <div class="space-y-6">
        <section class="rounded-xl bg-white p-5 shadow-card sm:p-6" aria-labelledby="d1">
            <h2 id="d1" class="mb-3 text-2xl">Enquiry</h2>
            <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                @foreach ($rows(['Received' => $e->created_at->format('j M Y H:i'), 'Form' => $e->form_name, 'Email' => $e->email, 'Phone' => $e->phone_e164, 'Package' => $e->package_name, 'Tour type' => $e->tour_type, 'Preferred date' => $e->preferred_date, 'Time of year' => $e->time_of_year, 'Group size' => $e->group_size, 'Nights' => $e->nights, 'Second quote' => $e->second_quote]) as $k => $v)
                    <div><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">{{ $k }}</dt><dd>{{ $v }}</dd></div>
                @endforeach
            </dl>
            @if ($e->special_request)<div class="mt-4"><p class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Special request</p><p class="mt-1 whitespace-pre-line">{{ $e->special_request }}</p></div>@endif
        </section>

        @if ($e->quiz_answers)
            <section class="rounded-xl bg-white p-5 shadow-card sm:p-6" aria-labelledby="d2">
                <h2 id="d2" class="mb-3 text-2xl">Quiz answers</h2>
                <dl class="space-y-2 text-sm">@foreach ($e->quiz_answers as $a)<div class="grid gap-1 sm:grid-cols-[14rem_1fr]"><dt class="text-ink-soft">{{ $a['question'] }}</dt><dd class="font-medium">{{ $a['answer'] }}</dd></div>@endforeach</dl>
            </section>
        @endif

        <section class="rounded-xl bg-white p-5 shadow-card sm:p-6" aria-labelledby="d3">
            <h2 id="d3" class="mb-3 text-2xl">Consent evidence</h2>
            <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Consent ticked</dt><dd>{{ $e->consent_given ? 'Yes' : 'No' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Timestamp</dt><dd>{{ $e->consent_at?->format('j M Y H:i:s T') }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Terms version</dt><dd>{{ $e->terms_version }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">IP address</dt><dd>{{ $e->ip_address }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Page URL</dt><dd class="break-all">{{ $e->page_url }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Browser</dt><dd class="break-all">{{ $e->user_agent }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Exact consent text shown</dt><dd class="rounded-lg bg-sand/70 p-3">{{ $e->consent_text }}</dd></div>
            </dl>
        </section>

        <section class="rounded-xl bg-white p-5 shadow-card sm:p-6" aria-labelledby="d4">
            <h2 id="d4" class="mb-3 text-2xl">Tracking</h2>
            <div class="grid gap-6 text-sm sm:grid-cols-2">
                @foreach (['First touch' => $e->tracking_first, 'Latest touch' => $e->tracking_last] as $title => $data)
                    <div><h3 class="mb-2 text-lg">{{ $title }}</h3>
                        @forelse (($data ?? []) as $k => $v)<p class="break-all"><span class="text-ink-soft">{{ $k }}:</span> {{ $v }}</p>@empty<p class="text-ink-soft">No data (direct visit).</p>@endforelse
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <aside class="space-y-6">
        <form method="POST" action="{{ route('admin.enquiries.update', $e) }}" class="space-y-4 rounded-xl bg-white p-5 shadow-card">
            @csrf @method('PUT')
            <h2 class="text-2xl">Follow-up</h2>
            <x-admin.select name="status" label="Status" :options="\App\Models\Enquiry::STATUSES" :value="$e->status"/>
            <x-admin.textarea name="notes" label="Internal notes" :value="$e->notes" rows="6"/>
            <button class="w-full rounded-full bg-cta-600 py-2.5 text-sm font-semibold text-white hover:bg-cta-700">Save</button>
        </form>

        <section class="rounded-xl bg-white p-5 shadow-card" aria-labelledby="ghl">
            <h2 id="ghl" class="mb-2 text-2xl">GoHighLevel</h2>
            <p class="text-sm"><span class="text-ink-soft">Status:</span> <strong>{{ ucfirst($e->ghl_status) }}</strong></p>
            @if ($e->ghl_contact_id)<p class="break-all text-sm"><span class="text-ink-soft">Contact ID:</span> {{ $e->ghl_contact_id }}</p>@endif
            @if ($e->ghl_synced_at)<p class="text-sm"><span class="text-ink-soft">Synced:</span> {{ $e->ghl_synced_at->format('j M Y H:i') }}</p>@endif
            @if ($e->ghl_error)<p class="mt-2 break-words rounded bg-red-50 p-2 text-xs text-red-800">{{ $e->ghl_error }}</p>@endif
            @if ($e->ghl_status !== 'synced')
                <form method="POST" action="{{ route('admin.enquiries.retry-ghl', $e) }}" class="mt-3">@csrf<button class="rounded-full border border-brand-300 px-4 py-1.5 text-sm font-semibold hover:bg-brand-50">Retry sync</button></form>
            @endif
        </section>

        <form method="POST" action="{{ route('admin.enquiries.destroy', $e) }}" x-data="{ ask: false }" class="rounded-xl bg-white p-5 shadow-card">
            @csrf @method('DELETE')
            <button type="button" x-show="!ask" @click="ask = true" class="text-sm font-semibold text-red-700 hover:underline">Delete this enquiry</button>
            <div x-show="ask" x-cloak class="text-sm"><p class="mb-2 text-red-800">This permanently removes the enquiry and its consent record. Sure?</p><button class="rounded-full bg-red-700 px-4 py-1.5 font-semibold text-white">Yes, delete</button> <button type="button" @click="ask = false" class="px-3 py-1.5">Cancel</button></div>
        </form>
    </aside>
</div>
@endsection

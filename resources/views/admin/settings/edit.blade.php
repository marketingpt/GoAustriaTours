@extends('layouts.admin', ['title' => 'Settings'])

@section('content')
<h1 class="mb-6 text-4xl">Site settings</h1>

<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf @method('PUT')
    @foreach ($groups as $group => $fields)
        <fieldset class="space-y-5 rounded-xl bg-white p-5 shadow-card sm:p-8">
            <legend class="float-left mb-2 w-full font-display text-2xl text-brand-800">{{ $group }}</legend>
            <div class="clear-both"></div>
            @foreach ($fields as $key => [$label, $type, $help])
                @if ($type === 'media')
                    <x-admin.media :name="$key" :label="$label" :selected="setting($key)" :hint="$help"/>
                @elseif ($type === 'textarea')
                    <x-admin.textarea :name="$key" :label="$label" :value="setting($key)" :hint="$help"/>
                @elseif ($type === 'password')
                    <x-admin.input :name="$key" :label="$label" type="password" autocomplete="new-password" :hint="$help.($secretSet ? ' A value is saved.' : '')"/>
                @else
                    <x-admin.input :name="$key" :label="$label" :value="setting($key)" :hint="$help"/>
                @endif
            @endforeach
        </fieldset>
    @endforeach

    <section class="rounded-xl bg-white p-5 shadow-card sm:p-8" aria-labelledby="ghl">
        <h2 id="ghl" class="mb-2 font-display text-2xl text-brand-800">GoHighLevel (read-only)</h2>
        <p class="text-sm">Status: <strong class="{{ $ghl['on'] ? 'text-brand-700' : 'text-gold-700' }}">{{ $ghl['on'] ? 'ON, enquiries sync to GHL' : 'OFF' }}</strong></p>
        <p class="text-sm text-ink-soft">API token: {{ $ghl['token'] }} · Location ID: {{ $ghl['location'] }}</p>
        <p class="mt-2 text-xs text-ink-soft">These live in the server's <code>.env</code> file (GHL_API_TOKEN, GHL_LOCATION_ID) and are never stored in the database or shown here.</p>
    </section>

    <button class="rounded-full bg-cta-600 px-8 py-3 font-semibold text-white hover:bg-cta-700">Save settings</button>
</form>
@endsection

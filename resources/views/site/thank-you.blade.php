@extends('layouts.site')

@section('content')
<section class="section-y">
    <div class="container-x max-w-2xl text-center">
        <x-icon name="check" class="mx-auto mb-6 h-16 w-16 rounded-full bg-brand-700 p-4 text-white"/>
        <h1 class="text-5xl">Thank you!</h1>
        <p class="mt-5 text-xl text-ink-soft">We have received your enquiry and a specialist will be in touch shortly.</p>
        <p class="mt-3 text-ink-soft">In the meantime, explore some of our journeys for inspiration.</p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <x-button :href="route('packages.index')" icon="arrow-right">Browse journeys</x-button>
            <x-button :href="route('home')" variant="outline">Back to home</x-button>
        </div>
    </div>
</section>

@if ($conversion)
    {{-- Fires once per enquiry (the session flag is consumed). Attach the Google Ads conversion to this event in GTM. --}}
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'generate_lead', form_name: @json($conversion['form_name']), enquiry_id: @json($conversion['id']) });
    </script>
@endif
@endsection

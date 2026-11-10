@php
    $phone = setting('phone');
    $email = setting('email');
@endphp
<div class="bg-brand-900 text-[0.8rem] text-white/90">
    <div class="container-x flex flex-wrap items-center justify-between gap-x-6 gap-y-1 py-2">
        <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
            @if ($phone)
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="inline-flex items-center gap-1.5 font-semibold text-white hover:text-gold-300">
                    <x-icon name="phone" class="h-4 w-4"/><span>{{ $phone }}</span>
                </a>
            @endif
            @if ($email)
                <a href="mailto:{{ $email }}" class="hidden items-center gap-1.5 hover:text-gold-300 sm:inline-flex">
                    <x-icon name="mail" class="h-4 w-4"/><span>{{ $email }}</span>
                </a>
            @endif
        </div>
        <div class="ml-auto flex items-center gap-4">
            <a href="{{ route('home') }}#start-your-adventure" class="hidden underline-offset-4 hover:text-gold-300 hover:underline sm:inline">Get Instant Quote</a>
            <button type="button" class="rounded-full bg-cta-600 px-4 py-1 text-xs font-semibold uppercase tracking-wider text-white hover:bg-cta-700"
                    x-data @click="$dispatch('open-enquiry', { form_name: 'Top Bar Button' })">Start Your Adventure</button>
        </div>
    </div>
</div>

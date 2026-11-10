{{-- One modal for the whole site. Open it with: $dispatch('open-enquiry', { form_name, heading, package_id, package_name, tour_type }) --}}
<div
    x-data="enquiryModal({ heading: {{ Js::from(setting('tour_modal_heading')) }}, form_name: 'Header Modal' })"
    x-on:open-enquiry.window="open($event.detail)"
    x-on:keydown.escape.window="isOpen && close()"
    x-show="isOpen" x-cloak
    class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center sm:p-6"
    role="dialog" aria-modal="true" aria-labelledby="enquiry-modal-title"
>
    <div class="absolute inset-0 bg-brand-900/75" @click="close()" aria-hidden="true"></div>
    <div x-show="isOpen" x-transition x-trap.noscroll="isOpen" class="relative max-h-[94vh] w-full max-w-xl overflow-y-auto rounded-t-2xl bg-ivory p-5 shadow-lift sm:rounded-2xl sm:p-8">
        <button type="button" class="absolute right-3 top-3 rounded-full p-2 text-brand-700 hover:bg-brand-100" @click="close()" aria-label="Close form"><x-icon name="x" class="h-5 w-5"/></button>
        <p class="eyebrow mb-1">Free, no-obligation</p>
        <h2 id="enquiry-modal-title" class="pr-8 text-3xl" x-text="heading">{{ setting('tour_modal_heading') }}</h2>
        <p x-show="intro" x-text="intro" class="mt-2 text-sm text-ink-soft"></p>
        <div class="mt-5">
            <x-enquiry-form form-id="modal-form" form-name="Header Modal" dynamic extended submit-label="Send my enquiry"/>
        </div>
    </div>
</div>

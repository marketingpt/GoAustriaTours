@extends('layouts.admin', ['title' => 'Pages'])

@section('content')
<h1 class="mb-2 text-4xl">Pages</h1>
<p class="mb-6 text-ink-soft">Edit the wording and search-engine details of each page. Testimonials and FAQs have their own menu items.</p>
<div class="overflow-x-auto rounded-xl bg-white shadow-card">
    <table class="w-full min-w-[32rem] text-left text-sm">
        <thead class="border-b border-brand-100 text-xs uppercase tracking-wider text-ink-soft"><tr><th class="px-4 py-3">Page</th><th>Address</th><th>Last edited</th><th></th></tr></thead>
        <tbody class="divide-y divide-brand-100">
            @foreach ($pages as $p)
                @php $url = ['home' => url('/'), 'about' => route('about'), 'contact' => route('contact'), 'terms' => route('terms'), 'privacy' => route('privacy')][$p->slug] ?? '#'; @endphp
                <tr><td class="px-4 py-3 font-medium">{{ $p->title }}@if ($p->noindex) <span class="ml-1 rounded bg-gold-100 px-1.5 text-xs">noindex</span>@endif</td><td><a href="{{ $url }}" target="_blank" class="text-brand-700 hover:underline">{{ parse_url($url, PHP_URL_PATH) ?: '/' }}</a></td><td>{{ $p->updated_at->diffForHumans() }}</td><td class="pr-4 text-right"><a href="{{ route('admin.pages.edit', $p->slug) }}" class="font-semibold text-brand-700 hover:underline">Edit</a></td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

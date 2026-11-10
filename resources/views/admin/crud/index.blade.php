@extends('layouts.admin', ['title' => $plural])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-4xl">{{ $plural }}</h1>
    <a href="{{ route("$route.create") }}" class="rounded-full bg-brand-700 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-800">Add {{ $singular }}</a>
</div>

<div class="overflow-x-auto rounded-xl bg-white shadow-card">
    <table class="w-full min-w-[40rem] text-left text-sm">
        <thead class="border-b border-brand-100 text-xs uppercase tracking-wider text-ink-soft">
            <tr>@foreach ($columns as $label => $_)<th class="px-4 py-3 font-semibold">{{ $label }}</th>@endforeach<th class="px-4 py-3 text-right font-semibold">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-brand-100">
            @forelse ($items as $item)
                <tr>
                    @foreach ($columns as $label => $fn)<td class="px-4 py-3 align-top">{{ $fn($item) }}</td>@endforeach
                    <td class="whitespace-nowrap px-4 py-3 text-right">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            @if ($sortable)
                                <form method="POST" action="{{ route("$route.move", $item) }}" class="inline">@csrf<input type="hidden" name="dir" value="up"><button class="rounded px-2 py-1 hover:bg-brand-50" aria-label="Move up">&uarr;</button></form>
                                <form method="POST" action="{{ route("$route.move", $item) }}" class="inline">@csrf<input type="hidden" name="dir" value="down"><button class="rounded px-2 py-1 hover:bg-brand-50" aria-label="Move down">&darr;</button></form>
                            @endif
                            @foreach ($extraActions as $label => $action)
                                <form method="POST" action="{{ route($action[0], $item) }}" class="inline">@csrf<button class="rounded px-2 py-1 text-brand-700 hover:bg-brand-50">{{ $label }}</button></form>
                            @endforeach
                            <a href="{{ route("$route.edit", $item) }}" class="rounded px-2 py-1 font-semibold text-brand-700 hover:bg-brand-50">Edit</a>
                            <form method="POST" action="{{ route("$route.destroy", $item) }}" class="inline" x-data="{ ask: false }">
                                @csrf @method('DELETE')
                                <button type="button" x-show="!ask" @click="ask = true" class="rounded px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                                <span x-show="ask" x-cloak class="inline-flex items-center gap-1"><span class="text-xs text-red-800">Sure?</span><button type="submit" class="rounded bg-red-700 px-2 py-1 text-xs font-semibold text-white">Yes, delete</button><button type="button" @click="ask = false" class="rounded px-2 py-1 text-xs">No</button></span>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ count($columns) + 1 }}" class="px-4 py-10 text-center text-ink-soft">Nothing here yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

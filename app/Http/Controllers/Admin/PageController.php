<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Html;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::all()->sortBy(fn ($p) => array_search($p->slug, ['home', 'about', 'contact', 'terms', 'privacy']))->values()]);
    }

    public function edit(string $slug)
    {
        $page = Page::bySlug($slug);

        return view('admin.pages.edit', ['page' => $page, 'homeFields' => $slug === 'home' ? config('home') : []]);
    }

    public function update(Request $request, string $slug)
    {
        $page = Page::bySlug($slug);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:100000'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'og_media_id' => ['nullable', 'exists:media,id'],
            'noindex' => ['boolean'],
            'content' => ['nullable', 'array'],
            'content.*' => ['nullable', 'string', 'max:2000'],
        ]);

        $content = $page->content ?? [];
        if ($slug === 'home') {
            foreach (collect(config('home'))->collapse() as $key => [$label, $default, $type]) {
                if (array_key_exists($key, $data['content'] ?? [])) {
                    $v = $data['content'][$key];
                    $content[$key] = $type === 'media' ? ($v ? (int) $v : null) : strip_tags((string) $v);
                }
            }
        } elseif ($slug === 'contact') {
            $content['intro'] = strip_tags((string) ($data['content']['intro'] ?? ''));
        }

        $page->update([
            'title' => $data['title'],
            'body' => in_array($slug, ['about', 'terms', 'privacy'], true) ? Html::clean($data['body'] ?? null) : $page->body,
            'content' => $content ?: null,
            'seo_title' => $data['seo_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'og_media_id' => $data['og_media_id'] ?? null,
            'noindex' => $request->boolean('noindex'),
        ]);

        return redirect()->route('admin.pages.edit', $slug)->with('status', 'Page saved.');
    }
}

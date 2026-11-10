<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Page;

class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = collect([[route('home'), Page::where('slug', 'home')->value('updated_at'), '1.0']]);
        $urls->push([route('packages.index'), Package::published()->max('updated_at'), '0.9']);
        $urls->push([route('destinations.index'), Destination::active()->max('updated_at'), '0.7']);

        foreach (Package::published()->where('noindex', false)->orderBy('sort_order')->get(['slug', 'updated_at']) as $p) {
            $urls->push([route('packages.show', $p->slug), $p->updated_at, '0.8']);
        }
        foreach (Destination::active()->where('noindex', false)->get(['slug', 'updated_at']) as $d) {
            $urls->push([route('destinations.show', $d->slug), $d->updated_at, '0.6']);
        }
        foreach (['about' => 'about', 'contact' => 'contact', 'terms' => 'terms', 'privacy' => 'privacy'] as $slug => $route) {
            $page = Page::where('slug', $slug)->first();
            if ($page && ! $page->noindex) {
                $urls->push([route($route), $page->updated_at, '0.4']);
            }
        }

        return response()->view('site.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $body = "User-agent: *\nDisallow: /admin\nDisallow: /thank-you\nDisallow: /enquiry\nAllow: /\n\nSitemap: ".route('sitemap')."\n";

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}

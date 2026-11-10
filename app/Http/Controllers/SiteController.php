<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Faq;
use App\Models\Media;
use App\Models\Package;
use App\Models\Page;
use App\Models\QuizStep;
use App\Models\Testimonial;
use App\Models\TourType;
use App\Support\Seo;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        $home = Page::bySlug('home');
        $hero = ($id = $home->content['hero_media_id'] ?? null) ? Media::find($id) : null;
        $faqs = Faq::active()->get();

        return view('site.home', [
            'home' => $home,
            'preload' => $hero, // also used as the hero image
            'steps' => QuizStep::forQuiz(),
            'tourTypes' => TourType::active()->with('media')->get(),
            'featured' => Package::published()->where('is_featured', true)->with(['media', 'tourType', 'destination'])->orderBy('sort_order')->take(3)->get(),
            'testimonials' => Testimonial::active()->take(3)->get(),
            'faqs' => $faqs,
            'seo' => Seo::make($home->seo_title ?: 'Austria Tours', $home->meta_description, $hero, $home->noindex, array_filter([Seo::faqPage($faqs)]), route('home')),
        ]);
    }

    public function packages(Request $request)
    {
        $type = $request->string('type')->toString();
        $destination = $request->string('destination')->toString();

        $packages = Package::published()->with(['media', 'tourType', 'destination'])
            ->when($type, fn ($q) => $q->whereHas('tourType', fn ($t) => $t->where('slug', $type)))
            ->when($destination, fn ($q) => $q->whereHas('destination', fn ($d) => $d->where('slug', $destination)))
            ->orderBy('sort_order')->get();

        $activeType = $type ? TourType::where('slug', $type)->first() : null;
        $activeDestination = $destination ? Destination::where('slug', $destination)->first() : null;
        $heading = $activeType ? "{$activeType->name} Austria Journeys" : 'Austria Journeys and Tour Packages';

        return view('site.packages-index', [
            'packages' => $packages,
            'tourTypes' => TourType::active()->get(),
            'destinations' => Destination::active()->get(),
            'activeType' => $activeType,
            'activeDestination' => $activeDestination,
            'heading' => $heading,
            'seo' => Seo::make($heading, $activeType?->description ?: 'Browse tailor-made Austria journeys: self-drive, chauffeur-driven and coach tours. Every itinerary can be adjusted. Request a free quote.', null, false, [
                Seo::breadcrumbs([['Home', route('home')], ['Journeys', route('packages.index')]]),
            ], route('packages.index')),
        ]);
    }

    public function package(string $slug)
    {
        $package = Package::published()->with(['media', 'tourType', 'destination', 'gallery', 'ogMedia'])->where('slug', $slug)->firstOrFail();
        $related = Package::published()->where('id', '!=', $package->id)->with(['media', 'tourType', 'destination'])
            ->when($package->tour_type_id, fn ($q) => $q->where('tour_type_id', $package->tour_type_id))->orderBy('sort_order')->take(3)->get();

        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $package->title,
            'description' => strip_tags($package->summary ?: $package->description),
            'image' => $package->media?->url(1600),
            'touristType' => $package->tourType?->name,
            'url' => route('packages.show', $package->slug),
            'provider' => ['@type' => 'TravelAgency', 'name' => setting('brand_name'), 'url' => url('/')],
            'itinerary' => $package->itinerary ? ['@type' => 'ItemList', 'itemListElement' => collect($package->itinerary)->values()->map(fn ($d, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $d['title'] ?? '', 'description' => $d['body'] ?? ''])->all()] : null,
            // Sample prices are placeholders: never published as structured data.
            'offers' => (! $package->is_sample && $package->price_from) ? ['@type' => 'Offer', 'price' => (string) $package->price_from, 'priceCurrency' => $package->currency, 'availability' => 'https://schema.org/InStock', 'url' => route('packages.show', $package->slug)] : null,
        ]);

        return view('site.package-show', [
            'package' => $package,
            'related' => $related,
            'seo' => Seo::make($package->seo_title ?: $package->title, $package->meta_description ?: $package->summary, $package->ogMedia ?: $package->media, $package->noindex, array_filter([
                $schema,
                Seo::breadcrumbs([['Home', route('home')], ['Journeys', route('packages.index')], [$package->title, route('packages.show', $package->slug)]]),
                Seo::faqPage($package->faqs ?? []),
            ])),
        ]);
    }

    public function destinations()
    {
        return view('site.destinations-index', [
            'destinations' => Destination::active()->with('media')->get(),
            'seo' => Seo::make('Austria Destinations', 'Explore Vienna, Salzburg, the Tyrol, the Salzkammergut lakes and the Wachau valley, then request a tailor-made Austria journey.', null, false, [
                Seo::breadcrumbs([['Home', route('home')], ['Destinations', route('destinations.index')]]),
            ]),
        ]);
    }

    public function destination(string $slug)
    {
        $destination = Destination::active()->with('media')->where('slug', $slug)->firstOrFail();
        $packages = Package::published()->where('destination_id', $destination->id)->with(['media', 'tourType', 'destination'])->orderBy('sort_order')->get();

        return view('site.destination-show', [
            'destination' => $destination,
            'packages' => $packages,
            'others' => Destination::active()->where('id', '!=', $destination->id)->get(),
            'seo' => Seo::make($destination->seo_title ?: $destination->name, $destination->meta_description ?: $destination->intro, $destination->media, $destination->noindex, [
                Seo::breadcrumbs([['Home', route('home')], ['Destinations', route('destinations.index')], [$destination->name, route('destinations.show', $destination->slug)]]),
            ]),
        ]);
    }

    public function about()
    {
        $page = Page::bySlug('about');

        return view('site.page', [
            'page' => $page, 'body' => $this->body($page),
            'seo' => $this->pageSeo($page, 'About'),
        ]);
    }

    public function contact()
    {
        $page = Page::bySlug('contact');

        return view('site.contact', [
            'page' => $page,
            'seo' => $this->pageSeo($page, 'Contact'),
        ]);
    }

    public function legal(string $slug)
    {
        $page = Page::bySlug($slug);

        return view('site.page', [
            'page' => $page, 'body' => $this->body($page), 'legal' => true,
            'seo' => $this->pageSeo($page, $page->title),
        ]);
    }

    public function thankYou(Request $request)
    {
        $enquiry = $request->session()->pull('enquiry'); // flashed once by EnquiryController, so reloads do not re-fire the conversion

        return view('site.thank-you', [
            'conversion' => $enquiry,
            'noModal' => true,
            'seo' => Seo::make('Thank you', 'We have received your enquiry.', null, true),
        ]);
    }

    private function body(Page $page): string
    {
        return str_replace('{company}', e(setting('legal_name')), (string) $page->body);
    }

    private function pageSeo(Page $page, string $crumb): array
    {
        return Seo::make($page->seo_title ?: $page->title, $page->meta_description, $page->ogMedia, $page->noindex, [
            Seo::breadcrumbs([['Home', route('home')], [$crumb, url()->current()]]),
        ]);
    }
}

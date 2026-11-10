<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Package;
use App\Models\TourType;
use Illuminate\Database\Seeder;

/**
 * SAMPLE packages only. Every name, itinerary and price is invented placeholder data (is_sample = true)
 * and is flagged on the site until replaced. See docs/reference/CONTENT_TO_CONFIRM.md.
 */
class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $t = TourType::pluck('id', 'slug');
        $d = Destination::pluck('id', 'slug');

        $rows = [
            ['Alpine Lakes & High Roads Self-Drive', 'alpine-lakes-high-roads-self-drive', 'self-drive', 'hallstatt-salzkammergut', 8, 2450, 'grossglockner-road', true,
                'Drive from Salzburg through the lake district and over a high Alpine road at your own pace.',
                ['Lakeside stay near Hallstatt', 'Scenic high Alpine road drive', 'Salzburg old town', 'Hotel and route notes provided'],
                [['Day 1-2: Salzburg', 'Arrive, collect your car and explore the old town.'], ['Day 3-5: Salzkammergut', 'Lake villages and mountain viewpoints, including Hallstatt.'], ['Day 6-8: High Alpine road', 'A scenic mountain drive and an overnight in the Alps.']]],
            ['Danube, Wachau & Vienna Self-Drive', 'danube-wachau-vienna-self-drive', 'self-drive', 'wachau-valley', 7, 2150, 'wachau-vineyards', false,
                'A relaxed drive along the Danube through vineyards and abbey towns, finishing in Vienna.',
                ['Wine-growing villages', 'Abbey and castle stops', 'Two nights in Vienna', 'Flexible daily routes'],
                [['Day 1-2: Vienna', 'Settle in and enjoy the city.'], ['Day 3-5: Wachau', 'Drive the Danube valley with vineyard and abbey stops.'], ['Day 6-7: Salzburg', 'Continue to Salzburg for the final nights.']]],
            ['Imperial Vienna & Salzburg, Chauffeur-Driven', 'imperial-vienna-salzburg-chauffeur', 'chauffeur-driven', 'vienna', 7, 4950, 'vienna-belvedere', true,
                'Palaces, concert halls and Baroque streets with a private driver between each stop.',
                ['Private driver-guide', 'Palace and museum visits', 'Evening of music', 'Hotel transfers included'],
                [['Day 1-3: Vienna', 'Palaces, coffeehouses and an evening concert.'], ['Day 4: Wachau', 'A scenic day on the Danube with a private driver.'], ['Day 5-7: Salzburg', 'Old town, fortress and a lakes day trip.']]],
            ['Tyrol & Innsbruck Private Chauffeur Journey', 'tyrol-innsbruck-private-chauffeur', 'chauffeur-driven', 'tyrol-innsbruck', 6, 4450, 'innsbruck-alps', false,
                'Mountain scenery and alpine villages, with a private driver handling every mile.',
                ['Private driver-guide', 'Cable car to alpine terrain', 'Village stays', 'Flexible pace'],
                [['Day 1-2: Innsbruck', 'Old town and a mountain cable car.'], ['Day 3-4: Alpine valleys', 'Quiet villages and scenic drives.'], ['Day 5-6: Return route', 'A final alpine stop before departure.']]],
            ['Highlights of Austria Small-Group Coach Tour', 'highlights-of-austria-coach-tour', 'coach-tours', 'vienna', 9, 2990, 'vienna-hofburg', true,
                'Vienna, the Wachau, Salzburg and the Alps in one guided coach journey.',
                ['Guided small-group coach touring', 'Hotels and luggage handling arranged', 'Vienna, Wachau, Salzburg, Tyrol', 'No driving or logistics'],
                [['Day 1-3: Vienna', 'Guided sightseeing in the capital.'], ['Day 4-5: Wachau & Salzburg', 'Danube valley, then Mozart\'s city.'], ['Day 6-9: Lakes & Tyrol', 'Salzkammergut lakes and the Tyrolean Alps.']]],
            ['Hallstatt & Salzkammergut Coach Holiday', 'hallstatt-salzkammergut-coach-holiday', 'coach-tours', 'hallstatt-salzkammergut', 7, 2250, 'hallstatt-lake', false,
                'A restful coach holiday based around the lakes and villages east of Salzburg.',
                ['Lake district base', 'Guided excursions', 'Hallstatt visit', 'Comfortable coach travel'],
                [['Day 1-2: Salzburg', 'Arrive and explore the old town.'], ['Day 3-6: Lake district', 'Daily excursions to lakes and villages.'], ['Day 7: Departure', 'Transfer to Salzburg.']]],
        ];

        foreach ($rows as $i => [$title, $slug, $type, $dest, $nights, $price, $img, $featured, $summary, $highlights, $itin]) {
            $pkg = Package::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'tour_type_id' => $t[$type], 'destination_id' => $d[$dest],
                'nights' => $nights, 'price_from' => $price, 'currency' => 'USD',
                'price_note' => 'per person, sample price [CONFIRM]',
                'summary' => $summary,
                'description' => "<p>{$summary}</p><p><strong>This is a sample package.</strong> The itinerary, inclusions and price are placeholders to be replaced with real details.</p>",
                'highlights' => $highlights,
                'itinerary' => array_map(fn ($x) => ['title' => $x[0], 'body' => $x[1]], $itin),
                'inclusions' => ['[CONFIRM: inclusions]', 'Accommodation (sample)', 'Itinerary planning'],
                'exclusions' => ['[CONFIRM: exclusions]', 'International flights', 'Travel insurance'],
                'faqs' => [['question' => 'Can this itinerary be changed?', 'answer' => 'Yes. Treat this sample as a starting point; tell us what you would like to adjust.']],
                'media_id' => MediaSeeder::id($img),
                'is_published' => true, 'is_featured' => $featured, 'is_sample' => true,
                'sort_order' => $i + 1,
                'meta_description' => mb_substr($summary.' Sample package, request a quote.', 0, 160),
            ]);

            if ($pkg->media_id && $pkg->gallery()->count() === 0) {
                $pkg->gallery()->attach([$pkg->media_id => ['sort_order' => 1]]);
            }
        }
    }
}

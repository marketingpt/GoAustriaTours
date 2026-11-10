<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['Vienna', 'vienna', 'Imperial grandeur and café culture', 'vienna-belvedere',
                'Austria\'s capital pairs imperial palaces with concert halls, coffee houses and a lively food scene.',
                '<p>Vienna was the seat of the Habsburg court for centuries, and its grand boulevards, palaces and museums still reflect that history. Visitors typically combine the Hofburg and Schönbrunn with an evening at a concert, a long stop in a traditional coffeehouse and a taste of local wine.</p><p>It works well as the start or end of a longer Austria journey, easily linked to Salzburg or the Wachau valley.</p>'],
            ['Salzburg', 'salzburg', 'Baroque old town beneath a fortress', 'salzburg-fortress',
                'A compact Baroque city on the edge of the Alps, birthplace of Mozart and gateway to the lakes.',
                '<p>Salzburg\'s historic centre is a UNESCO World Heritage Site, overlooked by the Hohensalzburg Fortress. It is easy to explore on foot, and a natural base for day trips to the Salzkammergut lakes and the mountains beyond.</p>'],
            ['Tyrol & Innsbruck', 'tyrol-innsbruck', 'Alpine peaks above a lively city', 'innsbruck-alps',
                'Innsbruck sits in a valley ringed by mountains, with cable cars from the old town to alpine terrain.',
                '<p>The Tyrol is Austria\'s alpine heartland: pastel-coloured riverside houses in Innsbruck, high mountain passes, quiet valleys and traditional villages. It suits hikers, drivers and anyone who simply wants big mountain scenery.</p>'],
            ['Hallstatt & Salzkammergut', 'hallstatt-salzkammergut', 'Lakes, mountains and storybook villages', 'hallstatt-church',
                'A UNESCO-listed lake district of clear water, steep peaks and picturesque villages.',
                '<p>The Salzkammergut is a region of lakes and mountains east of Salzburg. Hallstatt, on its namesake lake, is among the most photographed villages in Europe, and quieter lakeside towns nearby offer a calmer way to enjoy the region.</p>'],
            ['Wachau Valley', 'wachau-valley', 'Vineyards, castles and the Danube', 'wachau-durnstein',
                'A terraced vineyard landscape along the Danube, known for its wine, abbeys and riverside villages.',
                '<p>The Wachau is a UNESCO-listed stretch of the Danube between Melk and Krems. Expect apricot orchards, terraced vineyards, medieval ruins and a gentle pace, ideal for a relaxed day or two between Vienna and Salzburg.</p>'],
        ];

        foreach ($rows as $i => [$name, $slug, $tagline, $img, $intro, $body]) {
            Destination::updateOrCreate(['slug' => $slug], [
                'name' => $name, 'tagline' => $tagline, 'intro' => $intro, 'body' => $body,
                'media_id' => MediaSeeder::id($img), 'sort_order' => $i + 1, 'is_active' => true,
                'seo_title' => "{$name} Tours and Travel Packages",
                'meta_description' => mb_substr("Plan your trip to {$name}, Austria. {$intro}", 0, 160),
            ]);
        }
    }
}

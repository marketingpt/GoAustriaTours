<?php

namespace Database\Seeders;

use App\Models\TourType;
use Illuminate\Database\Seeder;

class TourTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['Self Drive', 'self-drive', 'Your pace, your route', 'Collect your car, follow a carefully planned route and stop wherever the views call. Hotels, itinerary and driving notes are arranged for you.', 'car', 'grossglockner-road'],
            ['Chauffeur Driven', 'chauffeur-driven', 'Sit back and take it all in', 'A private driver-guide takes care of the roads while you enjoy the scenery, with the flexibility to linger or detour.', 'user', 'salzburg-fortress'],
            ['Coach Tours', 'coach-tours', 'Effortless, sociable touring', 'Travel with a small group on a comfortable coach with a guide, moving between Austria\'s highlights without any logistics to manage.', 'bus', 'melk-abbey'],
        ];

        foreach ($types as $i => [$name, $slug, $tagline, $desc, $icon, $img]) {
            TourType::updateOrCreate(['slug' => $slug], [
                'name' => $name, 'tagline' => $tagline, 'description' => $desc, 'icon' => $icon,
                'media_id' => MediaSeeder::id($img), 'sort_order' => $i + 1, 'is_active' => true,
            ]);
        }
    }
}

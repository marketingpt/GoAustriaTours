<?php

namespace Database\Seeders;

use App\Models\QuizStep;
use Illuminate\Database\Seeder;

/** Default hero quiz (the GoIrishTours flow without the destination step). Editable in Admin > Quiz. */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        if (QuizStep::exists()) {
            return; // never overwrite edits made in the admin
        }

        $nights = collect(range(3, 13))->map(fn ($n) => ["{$n} nights", null])->all();
        $nights[] = ['14+ nights', null];
        $people = collect(range(1, 9))->map(fn ($n) => [$n === 1 ? '1 person' : "{$n} people", null])->all();
        $people[] = ['10+ people', null];

        $steps = [
            ['time_of_year', 'Time of Year', 'What time of year do you want to travel?', 'tiles', [['Spring', 'flower'], ['Summer', 'sun'], ['Autumn', 'leaf'], ['Winter', 'snowflake']]],
            ['travel_style', 'Travel Style', 'What is your favourite way to see a new country?', 'tiles', [['By coach with other travellers', 'bus'], ['Driving yourself', 'car'], ['With a tour guide driving', 'user'], ["Don't know", 'question']]],
            ['group_size', 'Group Size', 'How many people are in your group?', 'select', $people],
            ['nights', 'Nights', 'How many nights?', 'select', $nights],
            ['flights_booked', 'Flights Booked', 'Have you booked flights?', 'tiles', [['Yes', 'check'], ['No', 'x']]],
            ['budget', 'Budget', 'Have you a budget in mind for your trip?', 'tiles', [['$2K per person', 'wallet'], ['$3K per person', 'wallet'], ['$5K per person', 'wallet'], ['$7K+ per person', 'wallet']]],
            ['likelihood', 'Likelihood', 'How likely are you to come?', 'tiles', [["I'm 100% sure I'm going", 'check'], ["I'm 80% sure", 'thumb'], ['50/50', 'scale'], ['Unsure, just researching', 'question']]],
        ];

        foreach ($steps as $i => [$key, $label, $question, $type, $options]) {
            $step = QuizStep::create(['field_key' => $key, 'label' => $label, 'question' => $question, 'type' => $type, 'sort_order' => $i + 1]);
            foreach ($options as $j => [$text, $icon]) {
                $step->options()->create(['label' => $text, 'icon' => $icon, 'sort_order' => $j + 1]);
            }
        }
    }
}

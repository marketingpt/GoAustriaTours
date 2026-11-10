<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Idempotent: safe to run more than once (uses updateOrCreate / existence checks). */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            MediaSeeder::class,
            TourTypeSeeder::class,
            DestinationSeeder::class,
            PackageSeeder::class,
            QuizSeeder::class,
            ContentSeeder::class,
        ]);
    }
}

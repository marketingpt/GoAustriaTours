<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Services\ImageService;
use Illuminate\Database\Seeder;

/** Imports database/seed-images/*.jpg (free-licence Pexels photos) through the WebP pipeline. */
class MediaSeeder extends Seeder
{
    private static array $cache = [];

    public static function id(string $name): ?int
    {
        return self::$cache[$name] ??= Media::where('original_name', "{$name}.jpg")->value('id');
    }

    public function run(ImageService $images): void
    {
        $dir = database_path('seed-images');
        $manifest = json_decode(file_get_contents("{$dir}/manifest.json"), true)['images'];

        foreach ($manifest as $name => $info) {
            if (Media::where('original_name', "{$name}.jpg")->exists() || ! is_file("{$dir}/{$name}.jpg")) {
                continue;
            }
            $images->store("{$dir}/{$name}.jpg", $info['alt'], "{$name}.jpg", "Pexels / {$info['photographer']} ({$info['url']})");
            $this->command?->info("Imported image: {$name}");
        }
    }
}

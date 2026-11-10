<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/** Turns an uploaded image into auto-oriented, resized WebP variants (480 / 800 / 1200 / 1600 wide). */
class ImageService
{
    public const SIZES = [480, 800, 1200, 1600];

    public function store(UploadedFile|string $file, string $alt, ?string $originalName = null, ?string $credit = null): Media
    {
        $manager = new ImageManager(new Driver);
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $original = $originalName ?? ($file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file));

        $master = $manager->read($path)->scaleDown(width: max(self::SIZES), height: max(self::SIZES));
        $base = 'media/'.now()->format('Y/m').'/'.Str::slug(pathinfo($original, PATHINFO_FILENAME) ?: 'image').'-'.Str::lower(Str::random(6));

        // Standard widths smaller than the master, plus the master itself (never upscale).
        $widths = collect(self::SIZES)->filter(fn ($w) => $w < $master->width())->push($master->width())->unique()->values();

        $sizes = [];
        foreach ($widths as $w) {
            $variant = $w >= $master->width() ? $master : $manager->read($path)->scaleDown(width: $w);
            Storage::disk('public')->put("{$base}-{$w}.webp", (string) $variant->toWebp(quality: 68));
            $sizes[] = $w;
        }

        return Media::create([
            'base_path' => $base,
            'original_name' => mb_substr($original, 0, 190),
            'alt' => $alt,
            'width' => $master->width(),
            'height' => $master->height(),
            'sizes' => $sizes,
            'credit' => $credit,
        ]);
    }
}

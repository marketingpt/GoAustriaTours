<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['base_path', 'original_name', 'alt', 'width', 'height', 'sizes', 'credit'];

    protected function casts(): array
    {
        return ['sizes' => 'array'];
    }

    /** Public URL of the variant closest to (and not below) $width, else the largest. */
    public function url(?int $width = null): string
    {
        $sizes = collect($this->sizes)->sort()->values();
        $pick = $width ? ($sizes->first(fn ($s) => $s >= $width) ?? $sizes->last()) : $sizes->last();

        return asset("storage/{$this->base_path}-{$pick}.webp");
    }

    public function srcset(): string
    {
        return collect($this->sizes)->sort()
            ->map(fn ($s) => asset("storage/{$this->base_path}-{$s}.webp")." {$s}w")
            ->implode(', ');
    }

    public function deleteFiles(): void
    {
        foreach ($this->sizes as $s) {
            Storage::disk('public')->delete("{$this->base_path}-{$s}.webp");
        }
    }
}

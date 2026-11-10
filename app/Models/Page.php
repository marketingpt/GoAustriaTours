<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'body', 'content', 'seo_title', 'meta_description', 'og_media_id', 'noindex'];

    protected function casts(): array
    {
        return ['content' => 'array', 'noindex' => 'boolean'];
    }

    public function ogMedia()
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    public static function bySlug(string $slug): self
    {
        return self::where('slug', $slug)->firstOrFail();
    }

    /** Structured text value with a default fallback. */
    public function text(string $key, string $default = ''): string
    {
        $v = $this->content[$key] ?? null;

        // Home page falls back to the defaults declared in config/home.php
        if (($v === null || $v === '') && $default === '' && $this->slug === 'home') {
            $default = (string) (collect(config('home'))->collapse()[$key][1] ?? '');
        }

        return ($v === null || $v === '') ? $default : (string) $v;
    }
}

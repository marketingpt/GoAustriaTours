<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'title', 'slug', 'tour_type_id', 'destination_id', 'nights', 'price_from', 'currency', 'price_note', 'summary',
        'description', 'highlights', 'itinerary', 'inclusions', 'exclusions', 'faqs', 'media_id', 'seo_title',
        'meta_description', 'og_media_id', 'noindex', 'is_published', 'is_featured', 'is_sample', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array', 'itinerary' => 'array', 'inclusions' => 'array', 'exclusions' => 'array', 'faqs' => 'array',
            'is_published' => 'boolean', 'is_featured' => 'boolean', 'is_sample' => 'boolean', 'noindex' => 'boolean',
            'price_from' => 'decimal:2',
        ];
    }

    public function tourType()
    {
        return $this->belongsTo(TourType::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function ogMedia()
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    public function gallery()
    {
        return $this->belongsToMany(Media::class, 'package_images')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function priceLabel(): ?string
    {
        if (! $this->price_from) {
            return null;
        }
        $symbol = ['USD' => '$', 'GBP' => '£', 'EUR' => '€'][$this->currency] ?? $this->currency.' ';

        return $symbol.number_format((float) $this->price_from);
    }
}

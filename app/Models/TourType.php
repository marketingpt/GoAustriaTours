<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourType extends Model
{
    protected $fillable = ['name', 'slug', 'tagline', 'description', 'icon', 'media_id', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}

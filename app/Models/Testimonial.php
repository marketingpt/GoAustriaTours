<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'location', 'quote', 'is_placeholder', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_placeholder' => 'boolean', 'is_active' => 'boolean'];
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}

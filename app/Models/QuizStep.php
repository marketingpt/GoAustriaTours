<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizStep extends Model
{
    protected $fillable = ['field_key', 'label', 'question', 'type', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class)->orderBy('sort_order');
    }

    public function activeOptions()
    {
        return $this->hasMany(QuizOption::class)->where('is_active', true)->orderBy('sort_order');
    }

    /** Active steps that have at least one active option, in order. */
    public static function forQuiz()
    {
        return self::where('is_active', true)->orderBy('sort_order')->with('activeOptions')->get()
            ->filter(fn ($s) => $s->activeOptions->isNotEmpty())->values();
    }
}

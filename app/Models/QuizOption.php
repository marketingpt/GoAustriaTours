<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    protected $fillable = ['quiz_step_id', 'label', 'icon', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function step()
    {
        return $this->belongsTo(QuizStep::class, 'quiz_step_id');
    }
}

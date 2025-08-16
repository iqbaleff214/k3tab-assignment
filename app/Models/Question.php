<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'title', 'question', 'choices', 'correct_answer_index',
    ];

    protected $casts = [
        'choices' => 'array',
    ];

    protected function title(): Attribute
    {
        return Attribute::get(fn () => $this->title ?? mb_substr($this->question, 0, 20, "UTF-8"));
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

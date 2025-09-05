<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'question', 'choices', 'correct_answer_index',
    ];

    protected $casts = [
        'choices' => 'array',
    ];

    protected function title(): Attribute
    {
        return Attribute::get(function () {
            $title = $this->title ?? mb_substr($this->question, 0, 20, "UTF-8");
            if (!filter_var($title, FILTER_VALIDATE_URL))
                return $title;

            return "Image question";
        });
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

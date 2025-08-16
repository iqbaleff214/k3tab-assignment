<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasUuids;
    use Sortable, Filterable, Paginate;

    protected $fillable = [
        'title', 'slug', 'description', 'body',
        'duration_estimation', 'minimum_score', 'questions_count',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Module $module) {
            if (empty($module->slug))
                $module->slug = Str::slug($module->title);
        });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediaable');
    }
}

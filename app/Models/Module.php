<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasUuids, HasFactory;
    use Sortable, Filterable, Paginate;

    protected $fillable = [
        'title', 'slug', 'description', 'body',
        'duration_estimation', 'minimum_score', 'questions_count',
        'code', 'equipment_required', 'procedure', 'reference', 'performance',
    ];

    protected $appends = [
        'available_questions_count',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Module $module) {
            if (empty($module->slug))
                $module->slug = Str::slug($module->title);
        });
    }

    protected function getAvailableQuestionsCountAttribute(): int
    {
        $toDisplay = $this->questions_count;
        $current = $this->questions()->count();
        return min($toDisplay, $current);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function assessees(): HasMany
    {
        return $this->hasMany(ModuleAssessee::class);
    }

    public function postTests(): HasMany
    {
        return $this->hasMany(PostTest::class);

    }
}

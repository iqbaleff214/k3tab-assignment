<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceGuide extends Model
{
    use HasUuids, HasFactory;
    use Filterable, Sortable, Paginate;

    protected $fillable = [
        'module_id', 'skill_number', 'title',
        'performance_task', 'tasks',
    ];

    protected $casts = [
        'tasks' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

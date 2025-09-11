<?php

namespace App\Models;

use App\Enum\AssessmentResult;
use App\Enum\AssessmentStatus;
use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasUuids;
    use Paginate, Filterable, Sortable;

    protected $fillable = [
        'assessor_id', 'assessee_id', 'assessee_name', 'assessee_no_id',
        'assessee_school', 'performance_guide_id', 'status', 'tasks', 'result',
        'comment', 'feedback', 'scheduled_at', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'tasks' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function scheduledAt(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $scheduledAt) => (empty($scheduledAt)) ?
                null : Carbon::createFromFormat('Y-m-d H:i:s', $scheduledAt, 'UTC'),
        );
    }

    public function enumStatus(): AssessmentStatus
    {
        return AssessmentStatus::tryFrom($this->status);
    }

    public function enumResult(): AssessmentResult
    {
        return AssessmentResult::tryFrom($this->result);
    }

    public function assessee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessee_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(PerformanceGuide::class, 'performance_guide_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(AssessmentSchedule::class, 'assessment_id');
    }
}

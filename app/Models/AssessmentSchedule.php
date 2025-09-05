<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentSchedule extends Model
{
    protected $fillable = [
        'assessment_id', 'proposed_date', 'status',
    ];

    protected $appends = [
        'current_status',
    ];

    protected $casts = [
        'proposed_date' => 'datetime',
    ];

    public function getCurrentStatusAttribute(): string
    {
        if ($this->status === null)
            return 'proposed';

        if ($this->status === 0)
            return 'rejected';

        return 'accepted';
    }

    public function proposedDate(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $proposedDate) => Carbon::createFromFormat('Y-m-d H:i:s', $proposedDate, 'UTC'),
        );
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

}

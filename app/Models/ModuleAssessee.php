<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ModuleAssessee extends Model
{
    protected $fillable = [
        'module_id', 'user_id', 'score',
        'downloaded_files', 'read_at', 'completed_at',
        'is_doing_test',
    ];

    protected $casts = [
        'downloaded_files' => 'array',
        'is_doing_test' => 'boolean',
    ];

    protected $appends = [
        'status',
    ];

    public function getStatusAttribute(): string
    {
        if ($this->is_doing_test)
            return 'doing_test';

        if (!$this->result()->exists())
            return 'read';

        $minimumScore = $this->module->minimum_score;
        if ($this->score < $minimumScore)
            return 'not_competent';

        return 'competent';
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function assessee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(PostTest::class, 'module_id', 'module_id')
            ->where('user_id', $this->user_id)
            ->latestOfMany();
    }

    public function results(): HasMany
    {
        return $this->hasMany(PostTest::class, 'module_id', 'module_id')
            ->where('user_id', $this->user_id)->latest();
    }
}

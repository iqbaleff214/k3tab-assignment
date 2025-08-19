<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function assessee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

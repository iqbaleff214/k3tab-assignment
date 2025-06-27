<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalFlow extends Model
{
    protected $fillable = [
        'subject', 'parent_id', 'role_id',
    ];

    public function children(): HasMany
    {
        return $this
            ->hasMany(ApprovalFlow::class, 'parent_id', 'id')
            ->with(['children', 'role']);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class, 'parent_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}

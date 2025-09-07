<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTest extends Model
{
    use Paginate, Filterable, Sortable;

    protected $fillable = [
        'module_id', 'user_id', 'answers',
        'score', 'minimum_score', 'is_passed',
    ];

    protected $casts = [
        'answers' => 'array',
        'is_passed' => 'boolean',
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

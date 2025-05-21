<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeSort(Builder $query, ?string $sort): void
    {
        $query->when(!empty($sort), function (Builder $query) use ($sort) {
            [$column, $order] = explode(':', $sort);
            if (!empty($column) && !empty($order)) {
                $query->orderBy($column, $order);
            }
        });
    }
}

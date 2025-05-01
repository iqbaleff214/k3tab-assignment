<?php

namespace App\Traits\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

trait Paginate
{
    protected int $sizePerPage = 50;

    public function scopeRender(Builder $query, ?int $page = null): LengthAwarePaginator
    {
        return $query
            ->paginate($page ?? $this->sizePerPage)
            ->withQueryString();
    }
}

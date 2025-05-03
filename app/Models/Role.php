<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;

class Role extends \Spatie\Permission\Models\Role
{
    use Filterable, Paginate;
}

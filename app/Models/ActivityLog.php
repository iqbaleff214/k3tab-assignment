<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    use Filterable, Sortable, Paginate;

    protected $casts = [
        'subject_id' => 'string',
        'properties' => 'array',
    ];

    protected $appends = [
        'module', 'icon',
    ];

    public function getModuleAttribute(): string
    {
        $class = str_replace('App\Models\\', '', $this->log_name);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));
    }

    public function getIconAttribute(): string
    {
        $module = $this->getModuleAttribute();
        return match ($module) {
            'user' => 'pi pi-user',
            'role' => 'pi pi-briefcase',
        };
    }
}

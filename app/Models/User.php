<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    use Filterable, Sortable, Paginate;
    use HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'phone',
        'has_whatsapp',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'avatar', 'international_phone',
    ];

    public function getAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF&background=FCD34D';
    }

    public function getInternationalPhoneAttribute(): string | null
    {
        if (empty($this->phone))
            return null;

        $number = $this->phone;
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0'))
            return '62' . substr($number, 1);

        if (str_starts_with($number, '62'))
            return $number;

        if (str_starts_with($number, '8'))
            return '62' . $number;

        if (str_starts_with($number, '620'))
            return '62' . substr($number, 3);

        return $number;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(self::class)
            ->logOnly(['name', 'email', 'phone'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => __('activity.created', [
                    'menu' => __('menu.user'),
                    'identifier' => $this->name,
                    'link' => '#',
                ]),
                'updated' => __('activity.updated', [
                    'menu' => __('menu.user'),
                    'identifier' => $this->name,
                    'link' => '#',
                ]),
                'deleted' => __('activity.deleted', [
                    'menu' => __('menu.user'),
                    'identifier' => $this->name,
                ]),
            });
    }
}

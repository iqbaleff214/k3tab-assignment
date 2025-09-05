<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Convert local datetime (Asia/Makassar) to UTC for DB.
     */
    public static function toUTC(string $datetime, string $tz = 'Asia/Makassar'): Carbon
    {
        return Carbon::parse($datetime, config('app.timezone') ?? $tz)->setTimezone('UTC');
    }

    /**
     * Convert UTC datetime from DB to local timezone.
     */
    public static function toLocal($datetime, string $tz = 'Asia/Makassar'): Carbon
    {
        return Carbon::parse($datetime, 'UTC')->setTimezone(config('app.timezone') ?? $tz);
    }
}

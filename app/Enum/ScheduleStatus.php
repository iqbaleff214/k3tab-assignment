<?php

namespace App\Enum;

enum ScheduleStatus: string
{
    case Proposed = 'proposed';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public static function values(): array
    {
        return array_map(fn (self $enum): string => $enum->value, self::cases());
    }
}

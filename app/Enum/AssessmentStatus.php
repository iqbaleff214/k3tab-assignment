<?php

namespace App\Enum;

enum AssessmentStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_map(fn (self $enum): string => $enum->value, self::cases());
    }
}

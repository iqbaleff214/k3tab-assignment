<?php

namespace App\Enum;

enum TaskStatus: string
{
    case Completed = 'completed';
    case NotCompleted = 'not_completed';
    case NotAvailable = 'not_available';

    public static function values(): array
    {
        return array_map(fn (self $enum): string => $enum->value, self::cases());
    }
}

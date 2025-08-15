<?php

namespace App\Enum;

enum UserType: string
{
    case Admin = 'admin';
    case Assessor = 'assessor';
    case Assessee = 'assessee';

    public static function values(): array
    {
        return array_map(fn (self $enum): string => $enum->value, self::cases());
    }
}

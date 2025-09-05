<?php

namespace App\Enum;

enum AssessmentResult: string
{
    case Competent = 'competent';
    case NotCompetent = 'not_competent';

    public static function values(): array
    {
        return array_map(fn (self $enum): string => $enum->value, self::cases());
    }
}

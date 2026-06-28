<?php

namespace App\Enums\Visa;

enum StaffType: string
{
    case REPRESENTATIVE = 'representative';
    case DRIVER = 'driver';
    case GUIDE = 'guide';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

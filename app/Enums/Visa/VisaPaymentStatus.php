<?php

namespace App\Enums\Visa;

enum VisaPaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

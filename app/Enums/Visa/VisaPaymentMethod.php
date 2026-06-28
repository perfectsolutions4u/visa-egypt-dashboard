<?php

namespace App\Enums\Visa;

enum VisaPaymentMethod: string
{
    case CARD = 'card';
    case PAYPAL = 'paypal';
    case CASH = 'cash';
    case WALLET = 'wallet';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

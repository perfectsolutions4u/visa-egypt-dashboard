<?php

namespace App\Enums\Visa;

enum WalletTransactionType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
    case BONUS = 'bonus';
    case REFUND = 'refund';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

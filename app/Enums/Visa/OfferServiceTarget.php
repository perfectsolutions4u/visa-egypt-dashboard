<?php

namespace App\Enums\Visa;

enum OfferServiceTarget: string
{
    case EXPLORE_EGYPT = 'explore_egypt';
    case MEET_ASSIST = 'meet_assist';
    case AIRPORT_TRANSFER = 'airport_transfer';
    case TRANSIT_TOUR = 'transit_tour';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

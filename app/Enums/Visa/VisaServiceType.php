<?php

namespace App\Enums\Visa;

enum VisaServiceType: string
{
    case MEET_ASSIST = 'meet_assist';
    case VISA_ON_ARRIVAL = 'visa_on_arrival';
    case AIRPORT_TRANSFER = 'airport_transfer';
    case TRANSIT_TOUR = 'transit_tour';
    case EXPLORE_EGYPT = 'explore_egypt';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::MEET_ASSIST => 'Meet & Assist',
            self::VISA_ON_ARRIVAL => 'Visa on Arrival',
            self::AIRPORT_TRANSFER => 'Airport Transfer',
            self::TRANSIT_TOUR => 'Transit Tour',
            self::EXPLORE_EGYPT => 'Explore Egypt',
        };
    }
}

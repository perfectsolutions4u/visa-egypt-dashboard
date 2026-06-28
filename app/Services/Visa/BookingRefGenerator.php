<?php

namespace App\Services\Visa;

use App\Models\Visa\VisaBooking;
use Illuminate\Support\Str;

class BookingRefGenerator
{
    public function generate(): string
    {
        do {
            $ref = 'VE'.Str::padLeft((string) random_int(0, 99999999), 8, '0');
        } while (VisaBooking::where('booking_ref', $ref)->exists());

        return $ref;
    }
}

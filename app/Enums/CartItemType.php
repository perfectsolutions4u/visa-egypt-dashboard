<?php

namespace App\Enums;



enum CartItemType: string
{
    case TOUR = 'tour';
    case RENTAL = 'rental';
    case HOTEL_ROOM_BOOKING = 'hotel_room_booking'; // Added new type
}




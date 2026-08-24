<?php

namespace App\Models\Visa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'type', 'name', 'max_passengers', 'max_bags', 'base_price', 'image', 'tags', 'is_active',
    ];

    protected $casts = [
        'max_passengers' => 'integer',
        'max_bags' => 'integer',
        'base_price' => 'float',
        'is_active' => 'boolean',
    ];

    public function visaBookings(): HasMany
    {
        return $this->hasMany(VisaBooking::class);
    }
}

<?php

namespace App\Models\Visa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'name', 'slug', 'duration', 'cities', 'highlights', 'itinerary',
        'inclusions', 'exclusions', 'starting_price', 'hero_image',
        'is_active', 'is_best_seller', 'sort_order',
    ];

    protected $casts = [
        'cities' => 'array',
        'highlights' => 'array',
        'itinerary' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'starting_price' => 'float',
        'is_active' => 'boolean',
        'is_best_seller' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function visaBookings(): HasMany
    {
        return $this->hasMany(VisaBooking::class);
    }
}

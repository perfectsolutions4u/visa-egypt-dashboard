<?php

namespace App\Models\Visa;

use App\Enums\Visa\VisaServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    protected $fillable = [
        'service_type', 'tier', 'name', 'price', 'features',
        'includes_visa', 'is_popular', 'duration_hours', 'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'features' => 'array',
        'includes_visa' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'service_type' => VisaServiceType::class,
    ];

    public function visaBookings(): HasMany
    {
        return $this->hasMany(VisaBooking::class);
    }
}

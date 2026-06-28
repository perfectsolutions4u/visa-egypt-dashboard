<?php

namespace App\Models\Visa;

use App\Enums\Visa\OfferServiceTarget;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'title', 'description', 'service_target', 'discount_percent',
        'membership_level', 'active_from', 'active_to', 'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'float',
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'is_active' => 'boolean',
        'service_target' => OfferServiceTarget::class,
    ];
}

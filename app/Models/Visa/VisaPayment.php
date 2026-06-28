<?php

namespace App\Models\Visa;

use App\Enums\Visa\VisaPaymentMethod;
use App\Enums\Visa\VisaPaymentStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaPayment extends Model
{
    protected $fillable = [
        'client_id', 'visa_booking_id', 'membership_id', 'subtotal', 'discount_type',
        'coupon_id', 'voucher_id', 'loyalty_discount',
        'points_used', 'points_earned', 'amount', 'currency',
        'method', 'status', 'gateway_reference',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'loyalty_discount' => 'float',
        'points_used' => 'integer',
        'points_earned' => 'integer',
        'amount' => 'float',
        'method' => VisaPaymentMethod::class,
        'status' => VisaPaymentStatus::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function visaBooking(): BelongsTo
    {
        return $this->belongsTo(VisaBooking::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}

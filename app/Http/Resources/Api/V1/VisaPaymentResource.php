<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VisaPaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'subtotal' => $this->subtotal,
            'discount_type' => $this->discount_type,
            'coupon_id' => $this->coupon_id,
            'voucher_id' => $this->voucher_id,
            'loyalty_discount' => $this->loyalty_discount,
            'discount_amount' => $this->loyalty_discount,
            'points_used' => $this->points_used,
            'points_earned' => $this->points_earned,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'method' => $this->method?->value ?? $this->method,
            'status' => $this->status?->value ?? $this->status,
            'gateway_reference' => $this->gateway_reference,
            'visa_booking_id' => $this->visa_booking_id,
            'membership_id' => $this->membership_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

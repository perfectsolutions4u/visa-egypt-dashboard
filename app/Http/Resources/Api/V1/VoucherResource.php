<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    public function toArray($request): array
    {
        $discountType = $this->discount_type?->value ?? $this->discount_type;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'discount_type' => $discountType,
            'discount_value' => $this->discount_value,
            'min_amount' => $this->min_amount,
            'service_target' => $this->service_target,
            'valid_from' => optional($this->valid_from)->format('Y-m-d'),
            'valid_to' => optional($this->valid_to)->format('Y-m-d'),
            'redeemed_at' => optional($this->pivot?->redeemed_at ?? $this->redeemed_at)->toIso8601String(),
            'discount_label' => $discountType === 'percentage'
                ? rtrim(rtrim(number_format((float) $this->discount_value, 2), '0'), '.') . '% OFF'
                : '$' . number_format((float) $this->discount_value, 2) . ' OFF',
        ];
    }
}

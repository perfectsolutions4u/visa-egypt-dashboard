<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'service_target' => $this->service_target?->value ?? $this->service_target,
            'discount_percent' => $this->discount_percent,
            'membership_level' => $this->membership_level,
            'active_from' => optional($this->active_from)->toIso8601String(),
            'active_to' => optional($this->active_to)->toIso8601String(),
        ];
    }
}

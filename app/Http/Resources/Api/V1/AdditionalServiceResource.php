<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AdditionalServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency ?: 'USD',
            'price_from' => (bool) $this->price_from,
            'icon' => $this->icon ?: 'local_offer',
            'accent_color' => $this->accent_color ?: '#0F2847',
            'features' => $this->features ?? [],
            'sort_order' => (int) $this->sort_order,
        ];
    }
}

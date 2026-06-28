<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'max_passengers' => $this->max_passengers,
            'max_bags' => $this->max_bags,
            'base_price' => $this->base_price,
            'image' => $this->image,
        ];
    }
}

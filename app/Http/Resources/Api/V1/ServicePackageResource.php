<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ServicePackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'service_type' => $this->service_type,
            'tier' => $this->tier,
            'name' => $this->name,
            'price' => $this->price,
            'duration_hours' => $this->duration_hours,
            'features' => $this->features,
            'includes_visa' => $this->includes_visa,
            'is_popular' => $this->is_popular,
        ];
    }
}

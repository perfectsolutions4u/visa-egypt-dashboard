<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'duration' => $this->duration,
            'cities' => $this->cities,
            'highlights' => $this->highlights,
            'itinerary' => $this->itinerary,
            'inclusions' => $this->inclusions,
            'exclusions' => $this->exclusions,
            'starting_price' => $this->starting_price,
            'hero_image' => $this->hero_image,
            'is_best_seller' => $this->is_best_seller,
        ];
    }
}

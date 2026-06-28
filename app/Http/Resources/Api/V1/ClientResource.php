<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'language' => $this->language,
            'nationality' => $this->nationality,
            'birthdate' => optional($this->birthdate)->format('Y-m-d'),
            'image' => $this->image
                ? (str_starts_with($this->image, 'http') ? $this->image : url($this->image))
                : null,
            'membership' => new MembershipResource($this->whenLoaded('activeMembership')),
            'wallet' => new WalletResource($this->whenLoaded('wallet')),
        ];
    }
}

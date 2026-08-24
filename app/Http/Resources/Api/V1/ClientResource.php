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
            'gender' => $this->gender,
            'passport_number' => $this->passport_number,
            'passport_expiry' => optional($this->passport_expiry)->format('Y-m-d'),
            'email_verified_at' => optional($this->email_verified_at)->toIso8601String(),
            'image' => self::publicImageUrl($this->image, $request),
            'membership' => new MembershipResource($this->whenLoaded('activeMembership')),
            'wallet' => new WalletResource($this->whenLoaded('wallet')),
        ];
    }

    public static function publicImageUrl(?string $image, $request = null): ?string
    {
        if ($image === null || trim($image) === '') {
            return null;
        }

        $value = trim($image);
        $relative = null;

        if (preg_match('#/(?:storage|api/v1/media)/(.+)$#i', $value, $matches)) {
            $relative = $matches[1];
        } elseif (str_starts_with($value, 'storage/')) {
            $relative = substr($value, strlen('storage/'));
        } elseif (str_starts_with($value, 'clients/')) {
            $relative = $value;
        } elseif (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        } else {
            $relative = ltrim($value, '/');
        }

        $relative = explode('?', str_replace('\\', '/', (string) $relative), 2)[0];
        $relative = ltrim($relative, '/');

        if ($relative === '' || str_contains($relative, '..')) {
            return null;
        }

        $base = $request
            ? $request->getSchemeAndHttpHost()
            : rtrim((string) config('app.url'), '/');

        return $base.'/api/v1/media/'.$relative;
    }
}

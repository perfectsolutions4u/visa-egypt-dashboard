<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VisaBookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'booking_ref' => $this->booking_ref,
            'service_type' => $this->service_type?->value ?? $this->service_type,
            'status' => $this->status?->value ?? $this->status,
            'travel_date' => optional($this->travel_date)->format('Y-m-d'),
            'travelers_count' => $this->travelers_count,
            'nationality' => $this->nationality,
            'contact_email' => $this->contact_email,
            'contact_whatsapp' => $this->contact_whatsapp,
            'flight_number' => $this->flight_number,
            'arrival_time' => $this->arrival_time,
            'meeting_point' => $this->meeting_point,
            'destination' => $this->destination,
            'special_requests' => $this->special_requests,
            'total_amount' => $this->total_amount,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'service_package' => new ServicePackageResource($this->whenLoaded('servicePackage')),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'assignment' => $this->whenLoaded('assignment', fn () => [
                'staff' => $this->assignment?->staff ? [
                    'id' => $this->assignment->staff->id,
                    'full_name' => $this->assignment->staff->full_name,
                    'type' => $this->assignment->staff->type?->value ?? $this->assignment->staff->type,
                    'phone' => $this->assignment->staff->phone,
                    'photo' => $this->assignment->staff->photo,
                ] : null,
                'vehicle' => $this->assignment?->vehicle ? new VehicleResource($this->assignment->vehicle) : null,
            ]),
            'current_tracking' => $this->whenLoaded('currentTrackingEvent', fn () => $this->currentTrackingEvent ? [
                'status_key' => $this->currentTrackingEvent->status_key,
                'status_label' => $this->currentTrackingEvent->status_label,
                'event_at' => $this->currentTrackingEvent->event_at?->toIso8601String(),
            ] : null),
            'payments' => $this->whenLoaded('payments', fn () => $this->payments->map(fn ($p) => [
                'id' => $p->id,
                'amount' => $p->amount,
                'currency' => $p->currency,
                'method' => $p->method?->value ?? $p->method,
                'status' => $p->status?->value ?? $p->status,
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

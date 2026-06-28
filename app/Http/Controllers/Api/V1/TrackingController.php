<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Visa\VisaBooking;
use App\Services\Visa\TrackingService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    use HasApiResponse;

    public function show(Request $request, VisaBooking $visaBooking, TrackingService $tracking)
    {
        abort_if($visaBooking->client_id !== $request->user()->id, 403);

        $visaBooking->load(['trackingEvents.staff', 'assignment.staff', 'assignment.vehicle']);

        return $this->send([
            'booking_ref' => $visaBooking->booking_ref,
            'status' => $visaBooking->status?->value ?? $visaBooking->status,
            'flow' => $tracking->flowFor($visaBooking),
            'events' => $visaBooking->trackingEvents->map(fn ($event) => [
                'status_key' => $event->status_key,
                'status_label' => $event->status_label,
                'event_at' => $event->event_at?->toIso8601String(),
                'is_current' => $event->is_current,
                'notes' => $event->notes,
                'staff' => $event->staff ? [
                    'full_name' => $event->staff->full_name,
                    'phone' => $event->staff->phone,
                    'photo' => $event->staff->photo,
                ] : null,
            ]),
            'assignment' => $visaBooking->assignment ? [
                'staff' => $visaBooking->assignment->staff?->only(['id', 'full_name', 'phone', 'photo', 'type']),
                'vehicle' => $visaBooking->assignment->vehicle?->only(['id', 'name', 'type']),
            ] : null,
        ]);
    }
}

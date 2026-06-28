<?php

namespace App\Services\Visa;

use App\Enums\Visa\VisaBookingStatus;
use App\Enums\Visa\VisaServiceType;
use App\Models\Visa\AppNotification;
use App\Models\Visa\TrackingEvent;
use App\Models\Visa\VisaBooking;
use Illuminate\Support\Collection;

class TrackingService
{
    public const FLOWS = [
        VisaServiceType::MEET_ASSIST->value => [
            'booking_confirmed' => 'Booking Confirmed',
            'staff_assigned' => 'Staff Assigned',
            'waiting_at_airport' => 'Waiting at Airport',
            'guest_met' => 'Guest Met',
            'service_completed' => 'Service Completed',
        ],
        VisaServiceType::AIRPORT_TRANSFER->value => [
            'driver_assigned' => 'Driver Assigned',
            'driver_arrived' => 'Driver Arrived',
            'passenger_picked_up' => 'Passenger Picked Up',
            'on_the_way' => 'On the Way',
            'arrived' => 'Arrived',
        ],
        VisaServiceType::TRANSIT_TOUR->value => [
            'guide_assigned' => 'Guide Assigned',
            'guest_picked_up' => 'Guest Picked Up',
            'tour_started' => 'Tour Started',
            'lunch_visits' => 'Lunch & Visits',
            'returned_to_airport' => 'Returned to Airport',
        ],
    ];

    public function flowFor(VisaBooking $booking): array
    {
        $type = $booking->service_type instanceof VisaServiceType
            ? $booking->service_type->value
            : $booking->service_type;

        return self::FLOWS[$type] ?? [];
    }

    public function advance(VisaBooking $booking, ?int $staffId = null, ?string $notes = null): ?TrackingEvent
    {
        $flow = $this->flowFor($booking);
        if ($flow === []) {
            return null;
        }

        $keys = array_keys($flow);
        $current = $booking->trackingEvents()->where('is_current', true)->first();
        $nextIndex = 0;

        if ($current) {
            $index = array_search($current->status_key, $keys, true);
            $nextIndex = $index === false ? 0 : min($index + 1, count($keys) - 1);
        }

        $statusKey = $keys[$nextIndex];
        $statusLabel = $flow[$statusKey];

        $booking->trackingEvents()->update(['is_current' => false]);

        $event = $booking->trackingEvents()->create([
            'status_key' => $statusKey,
            'status_label' => $statusLabel,
            'event_at' => now(),
            'staff_id' => $staffId,
            'is_current' => true,
            'notes' => $notes,
        ]);

        if ($nextIndex === 0 && $booking->status === VisaBookingStatus::ASSIGNED) {
            $booking->update(['status' => VisaBookingStatus::IN_PROGRESS]);
        }

        if ($statusKey === array_key_last($flow)) {
            $booking->update(['status' => VisaBookingStatus::COMPLETED]);
        }

        $this->notifyClient($booking, 'Service status updated', $statusLabel, 'live_tracking', $booking->booking_ref);

        return $event;
    }

    public function notifyClient(VisaBooking $booking, string $title, string $body, string $screen, ?string $targetId = null): void
    {
        if (! $booking->client_id) {
            return;
        }

        AppNotification::create([
            'client_id' => $booking->client_id,
            'title' => $title,
            'body' => $body,
            'type' => 'tracking',
            'target_screen' => $screen,
            'target_id' => $targetId,
        ]);
    }

    public function activeBookings(): Collection
    {
        return VisaBooking::with(['client', 'assignment.staff', 'currentTrackingEvent'])
            ->whereIn('status', [VisaBookingStatus::ASSIGNED, VisaBookingStatus::IN_PROGRESS])
            ->latest()
            ->get();
    }
}

<?php

namespace App\Http\Controllers\StaffPortal;

use App\Enums\Visa\VisaBookingStatus;
use App\Enums\Visa\VisaServiceType;
use App\Http\Controllers\Controller;
use App\Models\Visa\VisaBooking;
use App\Services\Visa\TrackingService;
use Illuminate\Http\Request;

class GuestRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = VisaBooking::with(['client', 'assignment.staff', 'currentTrackingEvent', 'servicePackage'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->input('service_type'));
        }

        if ($request->boolean('mine')) {
            $staffId = auth()->user()?->staffProfile?->id;
            if ($staffId) {
                $query->whereHas('assignment', fn ($q) => $q->where('staff_id', $staffId));
            }
        }

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('booking_ref', 'like', "%{$term}%")
                    ->orWhere('contact_email', 'like', "%{$term}%")
                    ->orWhere('flight_number', 'like', "%{$term}%")
                    ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$term}%"));
            });
        }

        $bookings = $query->paginate(20)->withQueryString();

        return view('staff-portal.requests.index', [
            'bookings' => $bookings,
            'statuses' => VisaBookingStatus::cases(),
            'serviceTypes' => VisaServiceType::cases(),
            'filters' => $request->only(['status', 'service_type', 'q', 'mine']),
        ]);
    }

    public function show(VisaBooking $visaBooking, TrackingService $tracking)
    {
        $visaBooking->load([
            'client',
            'program',
            'servicePackage',
            'vehicle',
            'assignment.staff',
            'assignment.vehicle',
            'trackingEvents.staff',
            'payments',
        ]);

        return view('staff-portal.requests.show', [
            'booking' => $visaBooking,
            'flow' => $tracking->flowFor($visaBooking),
        ]);
    }

    public function advance(Request $request, VisaBooking $visaBooking, TrackingService $tracking)
    {
        $staffId = auth()->user()?->staffProfile?->id;
        $tracking->advance($visaBooking, $staffId, $request->input('notes'));

        session()->flash('message', 'Status updated for guest.');
        session()->flash('type', 'success');

        return back();
    }

    public function complete(VisaBooking $visaBooking, TrackingService $tracking)
    {
        while ($visaBooking->fresh()->status !== VisaBookingStatus::COMPLETED) {
            $staffId = auth()->user()?->staffProfile?->id;
            $event = $tracking->advance($visaBooking, $staffId);
            if (! $event) {
                break;
            }
            $visaBooking->refresh();
        }

        session()->flash('message', 'Service marked as completed.');
        session()->flash('type', 'success');

        return redirect()->route('staff.requests.index');
    }

    public function updateNote(Request $request, VisaBooking $visaBooking)
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:5000']]);

        $visaBooking->update(['notes' => $request->input('notes')]);

        session()->flash('message', 'Follow-up notes saved.');
        session()->flash('type', 'success');

        return back();
    }
}

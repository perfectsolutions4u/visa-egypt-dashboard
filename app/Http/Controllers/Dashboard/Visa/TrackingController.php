<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\Http\Controllers\Controller;
use App\Models\Visa\VisaBooking;
use App\Services\Visa\TrackingService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(TrackingService $tracking)
    {
        return view('dashboard.visa.tracking.index', [
            'bookings' => $tracking->activeBookings(),
            'flows' => TrackingService::FLOWS,
        ]);
    }

    public function show(VisaBooking $visaBooking, TrackingService $tracking)
    {
        $visaBooking->load(['client', 'assignment.staff', 'assignment.vehicle', 'trackingEvents.staff']);

        return view('dashboard.visa.tracking.show', [
            'booking' => $visaBooking,
            'flow' => $tracking->flowFor($visaBooking),
            'staff' => \App\Models\Visa\Staff::where('is_active', true)->get(),
            'vehicles' => \App\Models\Visa\Vehicle::where('is_active', true)->get(),
        ]);
    }

    public function advance(Request $request, VisaBooking $visaBooking, TrackingService $tracking)
    {
        $tracking->advance($visaBooking, $request->integer('staff_id') ?: null, $request->input('notes'));
        session()->flash('message', 'Tracking status updated.');
        session()->flash('type', 'success');

        return back();
    }

    public function complete(VisaBooking $visaBooking, TrackingService $tracking)
    {
        while ($visaBooking->fresh()->status !== \App\Enums\Visa\VisaBookingStatus::COMPLETED) {
            $event = $tracking->advance($visaBooking);
            if (! $event) {
                break;
            }
            $visaBooking->refresh();
        }

        session()->flash('message', 'Service marked as completed.');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.tracking.index');
    }
}

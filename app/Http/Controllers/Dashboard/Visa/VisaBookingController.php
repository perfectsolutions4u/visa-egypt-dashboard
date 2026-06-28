<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\Enums\Visa\VisaBookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\VisaBookingRequest;
use App\Models\Visa\Staff;
use App\Models\Visa\Vehicle;
use App\Models\Visa\VisaBooking;
use App\Models\Visa\VisaBookingAssignment;
use App\Services\Visa\TrackingService;
use App\DataTables\Visa\VisaBookingDataTable;
use App\Services\Visa\BookingRefGenerator;
use Illuminate\Http\Request;

class VisaBookingController extends Controller
{
    public function index(VisaBookingDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.visa-bookings.index', [
            'statuses' => VisaBookingStatus::cases(),
        ]);
    }

    public function show(VisaBooking $visaBooking)
    {
        $visaBooking->load(['client', 'program', 'servicePackage', 'vehicle', 'assignment.staff', 'trackingEvents.staff']);

        return view('dashboard.visa.visa-bookings.show', [
            'booking' => $visaBooking,
            'staff' => Staff::where('is_active', true)->get(),
            'vehicles' => Vehicle::where('is_active', true)->get(),
        ]);
    }

    public function update(VisaBookingRequest $request, VisaBooking $visaBooking)
    {
        $visaBooking->update($request->getSanitized());
        session()->flash('message', 'Booking updated successfully.');
        session()->flash('type', 'success');

        return back();
    }

    public function confirm(VisaBooking $visaBooking, TrackingService $tracking)
    {
        $visaBooking->update(['status' => VisaBookingStatus::CONFIRMED]);
        $tracking->notifyClient($visaBooking, 'Booking confirmed', 'Your booking has been confirmed.', 'booking_details', $visaBooking->booking_ref);
        session()->flash('message', 'Booking confirmed.');
        session()->flash('type', 'success');

        return back();
    }

    public function cancel(VisaBooking $visaBooking, TrackingService $tracking)
    {
        $visaBooking->update(['status' => VisaBookingStatus::CANCELLED]);
        $tracking->notifyClient($visaBooking, 'Booking cancelled', 'Your booking was cancelled.', 'my_bookings', $visaBooking->booking_ref);
        session()->flash('message', 'Booking cancelled.');
        session()->flash('type', 'success');

        return back();
    }

    public function accept(VisaBooking $visaBooking, TrackingService $tracking)
    {
        $visaBooking->update(['status' => VisaBookingStatus::CONFIRMED]);
        $tracking->notifyClient($visaBooking, 'Explore Egypt request accepted', 'Your program request was accepted.', 'booking_details', $visaBooking->booking_ref);
        session()->flash('message', 'Explore Egypt booking accepted.');
        session()->flash('type', 'success');

        return back();
    }

    public function reject(Request $request, VisaBooking $visaBooking, TrackingService $tracking)
    {
        $visaBooking->update([
            'status' => VisaBookingStatus::REJECTED,
            'notes' => $request->input('reason', $visaBooking->notes),
        ]);
        $tracking->notifyClient($visaBooking, 'Booking rejected', $request->input('reason', 'Your request was rejected.'), 'my_bookings', $visaBooking->booking_ref);
        session()->flash('message', 'Booking rejected.');
        session()->flash('type', 'success');

        return back();
    }

    public function assign(Request $request, VisaBooking $visaBooking)
    {
        $data = $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
        ]);

        VisaBookingAssignment::updateOrCreate(
            ['visa_booking_id' => $visaBooking->id],
            [
                'staff_id' => $data['staff_id'],
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]
        );

        $visaBooking->update(['status' => VisaBookingStatus::ASSIGNED]);

        app(TrackingService::class)->notifyClient(
            $visaBooking,
            'Service assigned',
            'A team member has been assigned to your booking.',
            'live_tracking',
            $visaBooking->booking_ref
        );

        session()->flash('message', 'Staff assigned successfully.');
        session()->flash('type', 'success');

        return back();
    }
}

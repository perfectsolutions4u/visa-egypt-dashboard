<?php

namespace App\Http\Controllers\StaffPortal;

use App\Enums\Visa\VisaBookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Visa\VisaBooking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $today = now()->toDateString();

        $stats = [
            'pending' => VisaBooking::where('status', VisaBookingStatus::PENDING)->count(),
            'confirmed' => VisaBooking::where('status', VisaBookingStatus::CONFIRMED)->count(),
            'active' => VisaBooking::whereIn('status', [
                VisaBookingStatus::ASSIGNED,
                VisaBookingStatus::IN_PROGRESS,
            ])->count(),
            'today_arrivals' => VisaBooking::whereDate('travel_date', $today)
                ->whereNotIn('status', [VisaBookingStatus::CANCELLED, VisaBookingStatus::REJECTED])
                ->count(),
        ];

        $recent = VisaBooking::with(['client', 'assignment.staff', 'currentTrackingEvent'])
            ->latest()
            ->limit(10)
            ->get();

        $needsAttention = VisaBooking::with(['client', 'currentTrackingEvent'])
            ->whereIn('status', [VisaBookingStatus::PENDING, VisaBookingStatus::CONFIRMED, VisaBookingStatus::ASSIGNED])
            ->orderBy('travel_date')
            ->limit(8)
            ->get();

        return view('staff-portal.dashboard', compact('stats', 'recent', 'needsAttention'));
    }
}

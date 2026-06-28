<?php

namespace App\Services\Visa;

use App\Enums\Visa\VisaBookingStatus;
use App\Enums\Visa\VisaPaymentStatus;
use App\Models\Visa\AppNotification;
use App\Models\Visa\VisaBooking;
use App\Models\Visa\VisaPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VisaStatsService
{
    public function todayBookingsCount(): int
    {
        return VisaBooking::whereDate('created_at', today())->count();
    }

    public function weekBookingsCount(): int
    {
        return VisaBooking::where('created_at', '>=', now()->subDays(7))->count();
    }

    public function monthRevenue(): float
    {
        return (float) VisaPayment::where('status', VisaPaymentStatus::COMPLETED)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    public function liveBookingsCount(): int
    {
        return VisaBooking::whereIn('status', [
            VisaBookingStatus::ASSIGNED,
            VisaBookingStatus::IN_PROGRESS,
        ])->count();
    }

    public function needsActionCount(): int
    {
        return VisaBooking::where('status', VisaBookingStatus::PENDING)
            ->whereDoesntHave('assignment')
            ->count();
    }

    public function serviceTypeChart(): array
    {
        return VisaBooking::query()
            ->selectRaw('service_type, COUNT(*) as total')
            ->groupBy('service_type')
            ->pluck('total', 'service_type')
            ->toArray();
    }

    public function pendingBookings(int $limit = 10): Collection
    {
        return VisaBooking::with('client')
            ->where('status', VisaBookingStatus::PENDING)
            ->latest()
            ->limit($limit)
            ->get();
    }
}

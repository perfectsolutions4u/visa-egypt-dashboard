<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Visa\VisaStatsService;

class DashboardHomeController extends Controller
{
    public function __invoke(VisaStatsService $stats)
    {
        return view('dashboard.home.index', [
            'visaStats' => $stats,
            'todayBookings' => $stats->todayBookingsCount(),
            'weekBookings' => $stats->weekBookingsCount(),
            'monthRevenue' => $stats->monthRevenue(),
            'liveBookings' => $stats->liveBookingsCount(),
            'needsAction' => $stats->needsActionCount(),
            'serviceChart' => $stats->serviceTypeChart(),
            'pendingBookings' => $stats->pendingBookings(),
        ]);
    }
}

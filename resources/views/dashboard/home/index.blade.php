@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Visa Egypt Dashboard">
            <li class="breadcrumb-item active">Dashboard</li>
        </x-dashboard.partials.breadcrumb>

        <div class="row">
            <x-dashboard.partials.box-card permission="visa-bookings.list" title="Today's Bookings" :count="$todayBookings" icon="calendar" color="primary"/>
            <x-dashboard.partials.box-card permission="visa-bookings.list" title="This Week" :count="$weekBookings" icon="trending-up" color="info"/>
            <x-dashboard.partials.box-card permission="visa-payments.list" title="Month Revenue" :count="'$'.number_format($monthRevenue, 0)" icon="dollar-sign" color="success"/>
            <x-dashboard.partials.box-card permission="tracking.list" title="Live Bookings" :count="$liveBookings" icon="radio" color="secondary"/>
            <x-dashboard.partials.box-card permission="visa-bookings.list" title="Needs Action" :count="$needsAction" icon="alert-circle" color="warning"/>
        </div>

        <div class="row">
            @can('visa-bookings.list')
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header"><h5>Bookings by Service Type</h5></div>
                    <div class="card-body">
                        <canvas id="visa-service-chart" height="220"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header"><h5>Pending Bookings</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordernone">
                            <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Travel</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($pendingBookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_ref }}</td>
                                    <td>{{ $booking->client?->name ?? '—' }}</td>
                                    <td>{{ $booking->service_type?->label() }}</td>
                                    <td>{{ optional($booking->travel_date)->format('Y-m-d') }}</td>
                                    @can('visa-bookings.show')
                                        <td><a href="{{ route('dashboard.visa-bookings.show', $booking) }}">View</a></td>
                                    @endcan
                                </tr>
                            @empty
                                <tr><td colspan="5">No pending bookings.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                        <a href="{{ route('dashboard.visa-bookings.index', ['status' => 'pending']) }}" class="btn btn-primary mt-3">View All</a>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const chartData = @json($serviceChart);
    const labels = Object.keys(chartData).map(k => k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
    const values = Object.values(chartData);
    const ctx = document.getElementById('visa-service-chart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#1B2A4A', '#2EC4B6', '#C9A227', '#4A90A4', '#8B7355']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
</script>
@endpush

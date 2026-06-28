@extends('layouts.staff-portal.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb title="Staff Overview">
        <li class="breadcrumb-item active">Dashboard</li>
    </x-dashboard.partials.breadcrumb>

    <div class="container-fluid">
        <x-dashboard.partials.message-alert />

        <div class="row">
            <div class="col-sm-6 col-xl-3">
                <div class="card o-hidden">
                    <div class="card-body">
                        <h6 class="mb-1">Pending</h6>
                        <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        <small class="text-muted">Awaiting review</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card o-hidden">
                    <div class="card-body">
                        <h6 class="mb-1">Confirmed</h6>
                        <h3 class="mb-0">{{ $stats['confirmed'] }}</h3>
                        <small class="text-muted">Ready for assignment</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card o-hidden">
                    <div class="card-body">
                        <h6 class="mb-1">Active</h6>
                        <h3 class="mb-0">{{ $stats['active'] }}</h3>
                        <small class="text-muted">Assigned or in progress</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card o-hidden">
                    <div class="card-body">
                        <h6 class="mb-1">Today's Arrivals</h6>
                        <h3 class="mb-0">{{ $stats['today_arrivals'] }}</h3>
                        <small class="text-muted">{{ now()->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Needs Follow-up</h5>
                        <a href="{{ route('staff.requests.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Guest</th>
                                    <th>Travel</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($needsAttention as $booking)
                                <tr>
                                    <td>{{ $booking->booking_ref }}</td>
                                    <td>{{ $booking->client?->name ?? $booking->contact_email }}</td>
                                    <td>{{ optional($booking->travel_date)->format('Y-m-d') ?? '—' }}</td>
                                    <td><span class="badge {{ $booking->status?->badgeClass() }}">{{ $booking->status?->label() }}</span></td>
                                    <td><a href="{{ route('staff.requests.show', $booking) }}" class="btn btn-xs btn-primary btn-sm">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No requests need attention right now.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Recent Guest Requests</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Ref</th><th>Service</th><th>Step</th></tr>
                            </thead>
                            <tbody>
                            @foreach($recent as $booking)
                                <tr>
                                    <td><a href="{{ route('staff.requests.show', $booking) }}">{{ $booking->booking_ref }}</a></td>
                                    <td>{{ $booking->service_type?->label() }}</td>
                                    <td>{{ $booking->currentTrackingEvent?->status_label ?? '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

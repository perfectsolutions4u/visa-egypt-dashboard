@extends('layouts.dashboard.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb title="Live Tracking">
        <li class="breadcrumb-item active">Tracking</li>
    </x-dashboard.partials.breadcrumb>
    <div class="container-fluid">
        <x-dashboard.partials.message-alert />
        <div class="card">
            <div class="card-header"><h5>Active Bookings</h5></div>
            <div class="card-body table-responsive">
                <table class="table">
                    <thead><tr><th>Ref</th><th>Client</th><th>Service</th><th>Status</th><th>Current Step</th><th></th></tr></thead>
                    <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_ref }}</td>
                            <td>{{ $booking->client?->name }}</td>
                            <td>{{ $booking->service_type?->label() }}</td>
                            <td>{{ $booking->status?->label() }}</td>
                            <td>{{ $booking->currentTrackingEvent?->status_label ?? '—' }}</td>
                            <td><a href="{{ route('dashboard.tracking.show', $booking) }}" class="btn btn-sm btn-primary">Control</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No active bookings.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb title="Visa Booking {{ $booking->booking_ref }}">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.visa-bookings.index') }}">Visa Bookings</a></li>
    </x-dashboard.partials.breadcrumb>
    <div class="container-fluid">
        <x-dashboard.partials.message-alert />
        <div class="row">
            <div class="col-md-4">
                <div class="card"><div class="card-header"><h5>Customer</h5></div><div class="card-body">
                    <p><strong>Name:</strong> {{ $booking->client?->name ?? '—' }}</p>
                    <p><strong>Email:</strong> {{ $booking->contact_email ?? $booking->client?->email }}</p>
                    <p><strong>WhatsApp:</strong> {{ $booking->contact_whatsapp ?? $booking->client?->whatsapp }}</p>
                    <p><strong>Nationality:</strong> {{ $booking->nationality }}</p>
                    <p><strong>Travelers:</strong> {{ $booking->travelers_count }}</p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card"><div class="card-header"><h5>Service Details</h5></div><div class="card-body">
                    <p><strong>Ref:</strong> {{ $booking->booking_ref }}</p>
                    <p><strong>Type:</strong> {{ $booking->service_type?->label() }}</p>
                    <p><strong>Travel Date:</strong> {{ optional($booking->travel_date)->format('Y-m-d') }}</p>
                    <p><strong>Flight:</strong> {{ $booking->flight_number ?? '—' }}</p>
                    <p><strong>Amount:</strong> {{ $booking->total_amount ? '$'.number_format($booking->total_amount,2) : '—' }}</p>
                    <p><strong>Status:</strong> <span class="badge {{ $booking->status?->badgeClass() }}">{{ $booking->status?->label() }}</span></p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card"><div class="card-header"><h5>Operations</h5></div><div class="card-body">
                    @can('visa-bookings.assign')
                    <form method="POST" action="{{ route('dashboard.visa-bookings.assign', $booking) }}" class="mb-3">
                        @csrf
                        <label>Assign Staff</label>
                        <select name="staff_id" class="form-control mb-2" required>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @selected($booking->assignment?->staff_id == $member->id)>{{ $member->full_name }} ({{ $member->type?->value }})</option>
                            @endforeach
                        </select>
                        <select name="vehicle_id" class="form-control mb-2">
                            <option value="">No vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected($booking->assignment?->vehicle_id == $vehicle->id)>{{ $vehicle->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary btn-sm">Assign</button>
                    </form>
                    @endcan
                    <div class="d-flex flex-wrap gap-1">
                        @can('visa-bookings.confirm')
                            <form method="POST" action="{{ route('dashboard.visa-bookings.confirm', $booking) }}">@csrf<button class="btn btn-success btn-sm">Confirm</button></form>
                            <form method="POST" action="{{ route('dashboard.visa-bookings.accept', $booking) }}">@csrf<button class="btn btn-info btn-sm">Accept (Explore)</button></form>
                        @endcan
                        @can('visa-bookings.cancel')
                            <form method="POST" action="{{ route('dashboard.visa-bookings.cancel', $booking) }}">@csrf<button class="btn btn-warning btn-sm">Cancel</button></form>
                        @endcan
                    </div>
                    @can('tracking.list')
                        <a href="{{ route('dashboard.tracking.show', $booking) }}" class="btn btn-outline-primary btn-sm mt-2">Live Tracking</a>
                    @endcan
                </div></div>
            </div>
        </div>
        @can('visa-bookings.edit')
        <form method="POST" action="{{ route('dashboard.visa-bookings.update', $booking) }}" class="card mt-3">
            @csrf @method('PUT')
            <div class="card-body">
                <label>Admin Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                <button class="btn btn-primary mt-2">Save Notes</button>
            </div>
        </form>
        @endcan
    </div>
</div>
@endsection

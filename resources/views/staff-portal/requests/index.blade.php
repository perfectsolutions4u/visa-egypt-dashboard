@extends('layouts.staff-portal.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb :title="request()->boolean('mine') ? 'My Assignments' : 'Guest Requests'">
        <li class="breadcrumb-item active">Requests</li>
    </x-dashboard.partials.breadcrumb>

    <div class="container-fluid">
        <x-dashboard.partials.message-alert />

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Ref, guest, flight…">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Service</label>
                        <select name="service_type" class="form-control">
                            <option value="">All services</option>
                            @foreach($serviceTypes as $type)
                                <option value="{{ $type->value }}" @selected(($filters['service_type'] ?? '') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="mine" value="1" class="form-check-input" id="mine" @checked(request()->boolean('mine'))>
                            <label class="form-check-label" for="mine">My assignments only</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ $bookings->total() }} request(s)</h5></div>
            <div class="card-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Guest</th>
                            <th>Service</th>
                            <th>Travel Date</th>
                            <th>Flight</th>
                            <th>Status</th>
                            <th>Current Step</th>
                            <th>Assigned</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td><strong>{{ $booking->booking_ref }}</strong></td>
                            <td>
                                {{ $booking->client?->name ?? '—' }}
                                @if($booking->contact_whatsapp)
                                    <br><small class="text-muted">{{ $booking->contact_whatsapp }}</small>
                                @endif
                            </td>
                            <td>{{ $booking->service_type?->label() }}</td>
                            <td>{{ optional($booking->travel_date)->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ $booking->flight_number ?? '—' }}</td>
                            <td><span class="badge {{ $booking->status?->badgeClass() }}">{{ $booking->status?->label() }}</span></td>
                            <td>{{ $booking->currentTrackingEvent?->status_label ?? '—' }}</td>
                            <td>{{ $booking->assignment?->staff?->full_name ?? '—' }}</td>
                            <td>
                                <a href="{{ route('staff.requests.show', $booking) }}" class="btn btn-sm btn-primary">Follow up</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9">No guest requests found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

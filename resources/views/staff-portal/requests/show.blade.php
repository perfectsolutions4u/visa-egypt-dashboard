@extends('layouts.staff-portal.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb title="Request {{ $booking->booking_ref }}">
        <li class="breadcrumb-item"><a href="{{ route('staff.requests.index') }}">Guest Requests</a></li>
    </x-dashboard.partials.breadcrumb>

    <div class="container-fluid">
        <x-dashboard.partials.message-alert />

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Guest</h5></div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $booking->client?->name ?? '—' }}</p>
                        <p><strong>Email:</strong> {{ $booking->contact_email ?? $booking->client?->email ?? '—' }}</p>
                        <p><strong>WhatsApp:</strong>
                            @if($booking->contact_whatsapp ?? $booking->client?->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->contact_whatsapp ?? $booking->client?->whatsapp) }}" target="_blank" rel="noopener">
                                    {{ $booking->contact_whatsapp ?? $booking->client?->whatsapp }}
                                </a>
                            @else
                                —
                            @endif
                        </p>
                        <p><strong>Nationality:</strong> {{ $booking->nationality ?? '—' }}</p>
                        <p><strong>Travelers:</strong> {{ $booking->travelers_count }}</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Trip Details</h5></div>
                    <div class="card-body">
                        <p><strong>Service:</strong> {{ $booking->service_type?->label() }}</p>
                        @if($booking->servicePackage)
                            <p><strong>Package:</strong> {{ $booking->servicePackage->name }}</p>
                        @endif
                        <p><strong>Travel date:</strong> {{ optional($booking->travel_date)->format('Y-m-d') ?? '—' }}</p>
                        <p><strong>Flight:</strong> {{ $booking->flight_number ?? '—' }}</p>
                        <p><strong>Arrival:</strong> {{ $booking->arrival_time ? \Illuminate\Support\Str::of($booking->arrival_time)->substr(0, 5) : '—' }}</p>
                        <p><strong>Meeting point:</strong> {{ $booking->meeting_point ?? '—' }}</p>
                        <p><strong>Destination:</strong> {{ $booking->destination ?? '—' }}</p>
                        <p><strong>Amount:</strong> {{ $booking->total_amount ? '$'.number_format($booking->total_amount, 2) : '—' }}</p>
                        <p><strong>Status:</strong> <span class="badge {{ $booking->status?->badgeClass() }}">{{ $booking->status?->label() }}</span></p>
                    </div>
                </div>

                @if($booking->assignment)
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Assignment</h5></div>
                    <div class="card-body">
                        <p><strong>Staff:</strong> {{ $booking->assignment->staff?->full_name ?? '—' }}</p>
                        <p><strong>Vehicle:</strong> {{ $booking->assignment->vehicle?->name ?? '—' }}</p>
                        <p><strong>Assigned:</strong> {{ optional($booking->assignment->assigned_at)->format('Y-m-d H:i') ?? '—' }}</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Service Timeline</h5></div>
                    <div class="card-body">
                        @if($flow === [])
                            <p class="text-muted mb-0">No tracking flow for this service type yet.</p>
                        @else
                            <ul class="list-group">
                                @foreach($flow as $key => $label)
                                    @php $event = $booking->trackingEvents->firstWhere('status_key', $key); @endphp
                                    <li class="list-group-item {{ $event?->is_current ? 'list-group-item-primary' : '' }}">
                                        <strong>{{ $label }}</strong>
                                        @if($event)
                                            <br><small>{{ $event->event_at->format('Y-m-d H:i') }}</small>
                                            @if($event->staff)
                                                <br><small class="text-muted">by {{ $event->staff->full_name }}</small>
                                            @endif
                                            @if($event->notes)
                                                <br><small>{{ $event->notes }}</small>
                                            @endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @can('guest-requests.advance')
                @if($flow !== [] && $booking->status !== \App\Enums\Visa\VisaBookingStatus::COMPLETED && $booking->status !== \App\Enums\Visa\VisaBookingStatus::CANCELLED)
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Update Status</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('staff.requests.advance', $booking) }}" class="mb-3">
                            @csrf
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" class="form-control mb-2" rows="3" placeholder="e.g. Met guest at gate 3"></textarea>
                            <button class="btn btn-primary w-100">Advance to Next Step</button>
                        </form>
                        <form method="POST" action="{{ route('staff.requests.complete', $booking) }}" onsubmit="return confirm('Mark this service as fully completed?')">
                            @csrf
                            <button class="btn btn-success w-100">Complete Service</button>
                        </form>
                    </div>
                </div>
                @endif
                @endcan

                @can('guest-requests.note')
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Follow-up Notes</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('staff.requests.update-note', $booking) }}">
                            @csrf
                            <textarea name="notes" class="form-control mb-2" rows="4">{{ old('notes', $booking->notes) }}</textarea>
                            <button class="btn btn-outline-primary w-100">Save Notes</button>
                        </form>
                    </div>
                </div>
                @endcan

                @if($booking->special_requests)
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Special Requests</h5></div>
                    <div class="card-body">
                        <pre class="mb-0 small">{{ is_array($booking->special_requests) ? json_encode($booking->special_requests, JSON_PRETTY_PRINT) : $booking->special_requests }}</pre>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

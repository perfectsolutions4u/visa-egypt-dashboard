@extends('layouts.dashboard.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb title="Tracking {{ $booking->booking_ref }}">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.tracking.index') }}">Tracking</a></li>
    </x-dashboard.partials.breadcrumb>
    <div class="container-fluid">
        <x-dashboard.partials.message-alert />
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Timeline</h5></div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($flow as $key => $label)
                                @php $event = $booking->trackingEvents->firstWhere('status_key', $key); @endphp
                                <li class="list-group-item {{ $event?->is_current ? 'list-group-item-primary' : '' }}">
                                    <strong>{{ $label }}</strong>
                                    @if($event)
                                        <br><small>{{ $event->event_at->format('Y-m-d H:i') }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Actions</h5></div>
                    <div class="card-body">
                        @can('tracking.advance')
                        <form method="POST" action="{{ route('dashboard.tracking.advance', $booking) }}" class="mb-3">
                            @csrf
                            <label>Staff (optional)</label>
                            <select name="staff_id" class="form-control mb-2">
                                <option value="">—</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                            <textarea name="notes" class="form-control mb-2" placeholder="Notes"></textarea>
                            <button class="btn btn-primary">Advance to Next Status</button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.tracking.complete', $booking) }}">
                            @csrf
                            <button class="btn btn-success">Complete Service</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

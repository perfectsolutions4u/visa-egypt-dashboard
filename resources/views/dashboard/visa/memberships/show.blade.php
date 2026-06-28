@extends('layouts.dashboard.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb title="Membership #{{ $membership->id }}" :hideFirst="true">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard.memberships.index') }}">Memberships</a>
        </li>
    </x-dashboard.partials.breadcrumb>

    <div class="container-fluid">
        <x-dashboard.partials.message-alert />

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Details</h5>
                        @can('memberships.edit')
                            <a href="{{ route('dashboard.memberships.edit', $membership) }}" class="btn btn-sm btn-primary">Edit</a>
                        @endcan
                    </div>
                    <div class="card-body">
                        <p><strong>Client:</strong>
                            @if($membership->client)
                                <a href="{{ route('dashboard.clients.show', $membership->client) }}">{{ $membership->client->name }}</a>
                            @else
                                —
                            @endif
                        </p>
                        <p><strong>Plan:</strong> {{ Str::headline($membership->plan_type) }}</p>
                        <p><strong>Discount:</strong> {{ $membership->discount_percent }}%</p>
                        <p><strong>Points:</strong> {{ $membership->points_balance }}</p>
                        <p><strong>Status:</strong> {{ Str::headline($membership->status) }}</p>
                        <p><strong>Start:</strong> {{ optional($membership->start_date)->format('Y-m-d') ?? '—' }}</p>
                        <p><strong>End:</strong> {{ optional($membership->end_date)->format('Y-m-d') ?? '—' }}</p>
                        <p><strong>Created:</strong> {{ $membership->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Related Payments</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($membership->payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ Str::headline($payment->method?->value ?? $payment->method) }}</td>
                                    <td>{{ Str::headline($payment->status?->value ?? $payment->status) }}</td>
                                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @can('visa-payments.show')
                                            <a href="{{ route('dashboard.visa-payments.show', $payment) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No payments linked to this membership.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

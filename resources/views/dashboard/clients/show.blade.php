@extends('layouts.dashboard.app')

@section('content')
<div class="page-body">
    <x-dashboard.partials.breadcrumb title="Client: {{ $client->name }}">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.clients.index') }}">Clients</a></li>
    </x-dashboard.partials.breadcrumb>
    <div class="container-fluid">
        <x-dashboard.partials.message-alert />

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#profile">Profile</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#membership">Membership</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#wallet">Wallet</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#visa-bookings">Visa Bookings</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payments">Payments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="profile">
                <div class="card"><div class="card-body">
                    <p><strong>Email:</strong> {{ $client->email }}</p>
                    <p><strong>Phone:</strong> {{ $client->phone }}</p>
                    <p><strong>WhatsApp:</strong> {{ $client->whatsapp }}</p>
                    <p><strong>Nationality:</strong> {{ $client->nationality }}</p>
                    <p><strong>Language:</strong> {{ $client->language }}</p>
                    @can('clients.edit')
                        <a href="{{ route('dashboard.clients.edit', $client) }}" class="btn btn-primary btn-sm">Edit</a>
                    @endcan
                </div></div>
            </div>

            <div class="tab-pane fade" id="membership">
                <div class="card"><div class="card-body">
                    @if($client->activeMembership)
                        <p><strong>Plan:</strong> {{ $client->activeMembership->plan_type }}</p>
                        <p><strong>Discount:</strong> {{ $client->activeMembership->discount_percent }}%</p>
                        <p><strong>Points:</strong> {{ $client->activeMembership->points_balance }}</p>
                    @else
                        <p>No active membership.</p>
                    @endif
                </div></div>
            </div>

            <div class="tab-pane fade" id="wallet">
                <div class="card"><div class="card-body">
                    <p><strong>Balance:</strong> ${{ number_format($client->wallet?->balance ?? 0, 2) }}</p>
                    <p><strong>Bonus:</strong> ${{ number_format($client->wallet?->bonus_credit ?? 0, 2) }}</p>

                    @can('clients.edit')
                    <form method="POST" action="{{ route('dashboard.clients.wallet.adjust', $client) }}" class="row g-2 mt-3">
                        @csrf
                        <div class="col-md-3"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount (+/-)" required></div>
                        <div class="col-md-5"><input type="text" name="description" class="form-control" placeholder="Description"></div>
                        <div class="col-md-2"><button class="btn btn-primary">Adjust</button></div>
                    </form>
                    @endcan

                    <h6 class="mt-4">Recent Transactions</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Type</th><th>Amount</th><th>Description</th><th>Date</th></tr></thead>
                        <tbody>
                        @forelse($client->wallet?->transactions ?? [] as $tx)
                            <tr>
                                <td>{{ $tx->type?->value ?? $tx->type }}</td>
                                <td>${{ number_format($tx->amount, 2) }}</td>
                                <td>{{ $tx->description }}</td>
                                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No transactions.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div></div>
            </div>

            <div class="tab-pane fade" id="visa-bookings">
                <div class="card"><div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>Ref</th><th>Service</th><th>Status</th><th>Travel</th><th></th></tr></thead>
                        <tbody>
                        @forelse($client->visaBookings as $booking)
                            <tr>
                                <td>{{ $booking->booking_ref }}</td>
                                <td>{{ $booking->service_type?->label() }}</td>
                                <td>{{ $booking->status?->label() }}</td>
                                <td>{{ optional($booking->travel_date)->format('Y-m-d') }}</td>
                                @can('visa-bookings.show')
                                    <td><a href="{{ route('dashboard.visa-bookings.show', $booking) }}">View</a></td>
                                @endcan
                            </tr>
                        @empty
                            <tr><td colspan="5">No visa bookings.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div></div>
            </div>

            <div class="tab-pane fade" id="payments">
                <div class="card"><div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>Amount</th><th>Status</th><th>Method</th><th>Date</th></tr></thead>
                        <tbody>
                        @forelse($client->visaPayments as $payment)
                            <tr>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->status?->value ?? $payment->status }}</td>
                                <td>{{ $payment->payment_method?->value ?? $payment->payment_method }}</td>
                                <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No payments.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>
    </div>
</div>
@endsection

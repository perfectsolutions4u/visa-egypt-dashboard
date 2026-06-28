@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Payment Details" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.visa-payments.index') }}">Visa Payments</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="order-success">
                                <h4>Payment #{{ $visaPayment->id }}</h4>
                                <ul class="order-details">
                                    <li>Client: {{ $visaPayment->client?->name ?? '—' }}</li>
                                    <li>Amount: {{ number_format($visaPayment->amount, 2) }} {{ $visaPayment->currency }}</li>
                                    <li>Method: {{ Str::headline($visaPayment->method?->value ?? $visaPayment->method) }}</li>
                                    <li>Status: {{ Str::headline($visaPayment->status?->value ?? $visaPayment->status) }}</li>
                                    <li>Gateway Reference: {{ $visaPayment->gateway_reference ?? '—' }}</li>
                                    <li>Visa Booking: {{ $visaPayment->visa_booking_id ?? '—' }}</li>
                                    <li>Membership: {{ $visaPayment->membership_id ?? '—' }}</li>
                                    <li>Created: {{ $visaPayment->created_at->format('Y-m-d H:i') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

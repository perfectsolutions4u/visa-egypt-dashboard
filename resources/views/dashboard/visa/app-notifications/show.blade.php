@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Notification Details" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.app-notifications.index') }}">App Notifications</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="order-success">
                                <h4>{{ $appNotification->title }}</h4>
                                <ul class="order-details">
                                    <li>Recipient: {{ $appNotification->client?->name ?? 'All Clients' }}</li>
                                    <li>Type: {{ $appNotification->type ?? '—' }}</li>
                                    <li>Target Screen: {{ $appNotification->target_screen ?? '—' }}</li>
                                    <li>Target ID: {{ $appNotification->target_id ?? '—' }}</li>
                                    <li>Read At: {{ optional($appNotification->read_at)->format('Y-m-d H:i') ?? 'Unread' }}</li>
                                    <li>Sent: {{ $appNotification->created_at->format('Y-m-d H:i') }}</li>
                                </ul>
                                @if($appNotification->body)
                                    <p class="mt-3">{{ $appNotification->body }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

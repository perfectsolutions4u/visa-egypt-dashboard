@php
    $staffTypes = collect(\App\Enums\Visa\StaffType::cases())
        ->mapWithKeys(fn ($type) => [$type->value => Str::headline($type->value)])
        ->all();
@endphp

@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.staff.store') }}" method="POST" class="page-body">
        @csrf

        <x-dashboard.partials.breadcrumb title="Create Staff" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.staff.index') }}">Staff</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-select :options="$staffTypes" error-key="type" name="type" id="type" label-title="Type"/>
                        <x-dashboard.form.input-text error-key="full_name" name="full_name" id="full_name" label-title="Full Name"/>
                        <x-dashboard.form.input-text error-key="phone" name="phone" id="phone" label-title="Phone"/>
                        <x-dashboard.form.input-text error-key="whatsapp" name="whatsapp" id="whatsapp" label-title="WhatsApp"/>
                        <x-dashboard.form.input-text error-key="rating" name="rating" id="rating" label-title="Rating"/>
                        <x-dashboard.form.input-text error-key="license_number" name="license_number" id="license_number" label-title="License Number"/>
                        <x-dashboard.form.input-text error-key="photo" name="photo" id="photo" label-title="Photo URL"/>
                        <x-dashboard.form.input-checkbox resource-name="Staff" error-key="is_active" :value="true" name="is_active" id="is_active" label-title="Active"/>
                        <x-dashboard.form.input-textarea error-key="languages" name="languages" id="languages" label-title="Languages (one per line)" placeholder="English&#10;Arabic&#10;French"/>

                        <hr>
                        <h6 class="mb-3">Staff Portal Login</h6>
                        <p class="text-muted small">Creates a user with the <strong>field_staff</strong> role for the staff portal.</p>
                        <x-dashboard.form.input-text error-key="login_email" name="login_email" id="login_email" label-title="Login Email"/>
                        <x-dashboard.form.input-text error-key="login_password" name="login_password" id="login_password" type="password" label-title="Login Password (min 8 characters)"/>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

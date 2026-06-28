@php
    $staffTypes = collect(\App\Enums\Visa\StaffType::cases())
        ->mapWithKeys(fn ($type) => [$type->value => Str::headline($type->value)])
        ->all();
    $languagesText = old(
        'languages',
        is_array($staff->languages) ? implode("\n", $staff->languages) : ''
    );
@endphp

@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.staff.update', $staff) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Staff" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.staff.index') }}">Staff</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-select :options="$staffTypes" :value="$staff->type?->value" error-key="type" name="type" id="type" label-title="Type"/>
                        <x-dashboard.form.input-text error-key="full_name" name="full_name" :value="$staff->full_name" id="full_name" label-title="Full Name"/>
                        <x-dashboard.form.input-text error-key="phone" name="phone" :value="$staff->phone" id="phone" label-title="Phone"/>
                        <x-dashboard.form.input-text error-key="whatsapp" name="whatsapp" :value="$staff->whatsapp" id="whatsapp" label-title="WhatsApp"/>
                        <x-dashboard.form.input-text error-key="rating" name="rating" :value="$staff->rating" id="rating" label-title="Rating"/>
                        <x-dashboard.form.input-text error-key="license_number" name="license_number" :value="$staff->license_number" id="license_number" label-title="License Number"/>
                        <x-dashboard.form.input-text error-key="photo" name="photo" :value="$staff->photo" id="photo" label-title="Photo URL"/>
                        <x-dashboard.form.input-checkbox resource-name="Staff" error-key="is_active" :value="$staff->is_active" name="is_active" id="is_active" label-title="Active"/>
                        <x-dashboard.form.input-textarea error-key="languages" name="languages" id="languages" label-title="Languages (one per line)" :value="$languagesText"/>

                        <hr>
                        <h6 class="mb-3">Staff Portal Login</h6>
                        <p class="text-muted small">Assigns the <strong>field_staff</strong> role. Leave password blank to keep the current password.</p>
                        <x-dashboard.form.input-text error-key="login_email" name="login_email" :value="old('login_email', $loginEmail)" id="login_email" label-title="Login Email"/>
                        <x-dashboard.form.input-text error-key="login_password" name="login_password" id="login_password" type="password" label-title="New Password (optional)"/>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

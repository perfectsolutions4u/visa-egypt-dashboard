@php
    $serviceTypes = collect(\App\Enums\Visa\VisaServiceType::cases())
        ->mapWithKeys(fn ($type) => [$type->value => $type->label()])
        ->all();
@endphp

@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.service-packages.store') }}" method="POST" class="page-body">
        @csrf

        <x-dashboard.partials.breadcrumb title="Create Service Package" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.service-packages.index') }}">Service Packages</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-select :options="$serviceTypes" error-key="service_type" name="service_type" id="service_type" label-title="Service Type"/>
                        <x-dashboard.form.input-text error-key="tier" name="tier" id="tier" label-title="Tier"/>
                        <x-dashboard.form.input-text error-key="name" name="name" id="name" label-title="Name"/>
                        <x-dashboard.form.input-text error-key="price" name="price" id="price" label-title="Price"/>
                        <x-dashboard.form.input-text error-key="duration_hours" name="duration_hours" id="duration_hours" label-title="Duration Hours"/>
                        <x-dashboard.form.input-checkbox resource-name="Package" error-key="includes_visa" name="includes_visa" id="includes_visa" label-title="Includes Visa"/>
                        <x-dashboard.form.input-checkbox resource-name="Package" error-key="is_popular" name="is_popular" id="is_popular" label-title="Popular"/>
                        <x-dashboard.form.input-checkbox resource-name="Package" error-key="is_active" :value="true" name="is_active" id="is_active" label-title="Active"/>
                        @include('dashboard.visa.service-packages._features_field')
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

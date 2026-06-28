@php
    $serviceTypes = collect(\App\Enums\Visa\VisaServiceType::cases())
        ->mapWithKeys(fn ($type) => [$type->value => $type->label()])
        ->all();
@endphp

@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.service-packages.update', $servicePackage) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Service Package" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.service-packages.index') }}">Service Packages</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-select :options="$serviceTypes" :value="$servicePackage->service_type?->value" error-key="service_type" name="service_type" id="service_type" label-title="Service Type"/>
                        <x-dashboard.form.input-text error-key="tier" name="tier" :value="$servicePackage->tier" id="tier" label-title="Tier"/>
                        <x-dashboard.form.input-text error-key="name" name="name" :value="$servicePackage->name" id="name" label-title="Name"/>
                        <x-dashboard.form.input-text error-key="price" name="price" :value="$servicePackage->price" id="price" label-title="Price"/>
                        <x-dashboard.form.input-text error-key="duration_hours" name="duration_hours" :value="$servicePackage->duration_hours" id="duration_hours" label-title="Duration Hours"/>
                        <x-dashboard.form.input-checkbox resource-name="Package" error-key="includes_visa" :value="$servicePackage->includes_visa" name="includes_visa" id="includes_visa" label-title="Includes Visa"/>
                        <x-dashboard.form.input-checkbox resource-name="Package" error-key="is_popular" :value="$servicePackage->is_popular" name="is_popular" id="is_popular" label-title="Popular"/>
                        <x-dashboard.form.input-checkbox resource-name="Package" error-key="is_active" :value="$servicePackage->is_active" name="is_active" id="is_active" label-title="Active"/>
                        @include('dashboard.visa.service-packages._features_field', ['servicePackage' => $servicePackage])
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@php
    $iconOptions = \App\Models\Visa\AdditionalService::iconOptions();
    $colorOptions = \App\Models\Visa\AdditionalService::colorOptions();
@endphp

@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.additional-services.update', $additionalService) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Additional Service" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.additional-services.index') }}">Additional Services</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="title" name="title" :value="$additionalService->title" id="title" label-title="Title"/>
                        <x-dashboard.form.input-textarea error-key="description" name="description" :value="$additionalService->description" id="description" label-title="Description"/>
                        <x-dashboard.form.input-text error-key="price" name="price" :value="$additionalService->price" id="price" label-title="Price"/>
                        <x-dashboard.form.input-text error-key="currency" name="currency" :value="$additionalService->currency" id="currency" label-title="Currency"/>
                        <x-dashboard.form.input-checkbox resource-name="Service" error-key="price_from" :value="$additionalService->price_from" name="price_from" id="price_from" label-title="Show From Before Price"/>
                        <x-dashboard.form.input-select :options="$iconOptions" :value="$additionalService->icon" error-key="icon" name="icon" id="icon" label-title="Icon"/>
                        <x-dashboard.form.input-select :options="$colorOptions" :value="$additionalService->accent_color" error-key="accent_color" name="accent_color" id="accent_color" label-title="Accent Color"/>
                        <x-dashboard.form.input-text error-key="sort_order" name="sort_order" :value="$additionalService->sort_order" id="sort_order" label-title="Sort Order"/>
                        <x-dashboard.form.input-checkbox resource-name="Service" error-key="is_active" :value="$additionalService->is_active" name="is_active" id="is_active" label-title="Active"/>
                        @include('dashboard.visa.additional-services._features_field', ['additionalService' => $additionalService])
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

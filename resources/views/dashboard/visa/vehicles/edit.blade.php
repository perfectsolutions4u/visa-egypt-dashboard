@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.vehicles.update', $vehicle) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Vehicle" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.vehicles.index') }}">Vehicles</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="type" name="type" :value="$vehicle->type" id="type" label-title="Type"/>
                        <x-dashboard.form.input-text error-key="name" name="name" :value="$vehicle->name" id="name" label-title="Name"/>
                        <x-dashboard.form.input-text error-key="max_passengers" name="max_passengers" :value="$vehicle->max_passengers" id="max_passengers" label-title="Max Passengers"/>
                        <x-dashboard.form.input-text error-key="max_bags" name="max_bags" :value="$vehicle->max_bags" id="max_bags" label-title="Max Bags"/>
                        <x-dashboard.form.input-text error-key="base_price" name="base_price" :value="$vehicle->base_price" id="base_price" label-title="Base Price (USD)"/>
                        <x-dashboard.form.input-text error-key="tags" name="tags" :value="$vehicle->tags" id="tags" label-title="Tags (comma separated)"/>
                        <x-dashboard.form.input-text error-key="image" name="image" :value="$vehicle->image" id="image" label-title="Image URL"/>
                        <x-dashboard.form.input-checkbox resource-name="Vehicle" error-key="is_active" :value="$vehicle->is_active" name="is_active" id="is_active" label-title="Active"/>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

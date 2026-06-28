@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.vehicles.store') }}" method="POST" class="page-body">
        @csrf

        <x-dashboard.partials.breadcrumb title="Create Vehicle" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.vehicles.index') }}">Vehicles</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="type" name="type" id="type" label-title="Type"/>
                        <x-dashboard.form.input-text error-key="name" name="name" id="name" label-title="Name"/>
                        <x-dashboard.form.input-text error-key="max_passengers" name="max_passengers" id="max_passengers" label-title="Max Passengers" :value="old('max_passengers', 3)"/>
                        <x-dashboard.form.input-text error-key="max_bags" name="max_bags" id="max_bags" label-title="Max Bags" :value="old('max_bags', 2)"/>
                        <x-dashboard.form.input-text error-key="image" name="image" id="image" label-title="Image URL"/>
                        <x-dashboard.form.input-checkbox resource-name="Vehicle" error-key="is_active" :value="true" name="is_active" id="is_active" label-title="Active"/>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

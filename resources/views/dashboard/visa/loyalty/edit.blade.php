@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.loyalty-settings.update') }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Loyalty Program" :hideFirst="true">
            <li class="breadcrumb-item active">Settings</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Loyalty Points</h5>
                            <span class="text-muted">Clients earn points when paying for services and redeem them for discounts at checkout.</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-4">
                                <div class="form-check form-switch">
                                    <input
                                        type="hidden"
                                        name="enabled"
                                        value="0"
                                    >
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="enabled"
                                        name="enabled"
                                        value="1"
                                        @checked(old('enabled', $settings['enabled']))
                                    >
                                    <label class="form-check-label" for="enabled">Enable loyalty program</label>
                                </div>
                            </div>

                            <x-dashboard.form.input-text
                                error-key="earn_points_per_usd"
                                required
                                type="number"
                                min="0"
                                :value="old('earn_points_per_usd', $settings['earn_points_per_usd'])"
                                name="earn_points_per_usd"
                                label-title="Earn points per $1 spent"
                            />

                            <x-dashboard.form.input-text
                                error-key="redeem_points_per_usd"
                                required
                                type="number"
                                min="1"
                                :value="old('redeem_points_per_usd', $settings['redeem_points_per_usd'])"
                                name="redeem_points_per_usd"
                                label-title="Points required for $1 discount"
                            />

                            <x-dashboard.form.input-text
                                error-key="min_points_redeem"
                                required
                                type="number"
                                min="0"
                                :value="old('min_points_redeem', $settings['min_points_redeem'])"
                                name="min_points_redeem"
                                label-title="Minimum points to redeem"
                            />

                            <x-dashboard.form.input-text
                                error-key="max_redeem_percent"
                                required
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                :value="old('max_redeem_percent', $settings['max_redeem_percent'])"
                                name="max_redeem_percent"
                                label-title="Maximum order % payable with points"
                            />

                            <div class="alert alert-light border">
                                <strong>Example:</strong>
                                With {{ $settings['earn_points_per_usd'] }} pts/$1 earned and
                                {{ $settings['redeem_points_per_usd'] }} pts = $1 off,
                                a $100 payment earns {{ 100 * (int) $settings['earn_points_per_usd'] }} points
                                (after any point discount is applied).
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

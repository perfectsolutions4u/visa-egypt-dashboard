@php
    $serviceTargets = collect(\App\Enums\Visa\OfferServiceTarget::cases())
        ->mapWithKeys(fn ($target) => [$target->value => Str::headline($target->value)])
        ->all();
    $membershipPlans = \App\Models\Visa\MembershipTier::optionsForSelect();
    if ($membershipPlans === []) {
        $membershipPlans = collect(\App\Enums\Visa\MembershipPlan::cases())
            ->mapWithKeys(fn ($plan) => [$plan->value => Str::headline($plan->value)])
            ->all();
    }
@endphp

@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.offers.update', $offer) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Offer" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.offers.index') }}">Offers</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="title" name="title" :value="$offer->title" id="title" label-title="Title"/>
                        <x-dashboard.form.input-textarea error-key="description" name="description" id="description" label-title="Description" :value="$offer->description"/>
                        <x-dashboard.form.input-select :options="$serviceTargets" :value="$offer->service_target?->value" error-key="service_target" name="service_target" id="service_target" label-title="Service Target"/>
                        <x-dashboard.form.input-text error-key="discount_percent" name="discount_percent" :value="$offer->discount_percent" id="discount_percent" label-title="Discount Percent"/>
                        <x-dashboard.form.input-select :options="$membershipPlans" :value="$offer->membership_level" error-key="membership_level" name="membership_level" id="membership_level" label-title="Membership Level"/>
                        <x-dashboard.form.input-text error-key="active_from" name="active_from" :value="optional($offer->active_from)->format('Y-m-d H:i')" id="active_from" label-title="Active From (Y-m-d H:i)"/>
                        <x-dashboard.form.input-text error-key="active_to" name="active_to" :value="optional($offer->active_to)->format('Y-m-d H:i')" id="active_to" label-title="Active To (Y-m-d H:i)"/>
                        <x-dashboard.form.input-checkbox resource-name="Offer" error-key="is_active" :value="$offer->is_active" name="is_active" id="is_active" label-title="Active"/>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

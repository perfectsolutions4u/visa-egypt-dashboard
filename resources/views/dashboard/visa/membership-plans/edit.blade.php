@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.membership-plans.update', $plan) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Membership Plan" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.membership-plans.manage') }}">Manage Plans</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        @include('dashboard.visa.membership-plans._form', ['plan' => $plan])
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

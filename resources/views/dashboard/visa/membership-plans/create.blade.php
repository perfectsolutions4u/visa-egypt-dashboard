@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.membership-plans.store') }}" method="POST" class="page-body">
        @csrf

        <x-dashboard.partials.breadcrumb title="Create Membership Plan" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.membership-plans.manage') }}">Manage Plans</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        @include('dashboard.visa.membership-plans._form', ['plan' => null])
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

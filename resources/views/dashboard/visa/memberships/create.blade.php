@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.memberships.store') }}" method="POST" class="page-body">
        @csrf

        <x-dashboard.partials.breadcrumb title="Create Membership" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.memberships.index') }}">Memberships</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        @include('dashboard.visa.memberships._form', ['membership' => null])
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.memberships.update', $membership) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Membership" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.memberships.index') }}">Memberships</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.memberships.show', $membership) }}">#{{ $membership->id }}</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        @include('dashboard.visa.memberships._form', ['membership' => $membership])
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Eligible Nationalities">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.visa-on-arrival.edit') }}">Visa On Arrival</a>
            </li>
            <li class="breadcrumb-item active">Nationalities</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert/>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Eligible Nationalities</h5>
                                <span class="text-muted">Shown and used for eligibility checks in the mobile app.</span>
                            </div>
                            <div>
                                <a href="{{ route('dashboard.visa-on-arrival.edit') }}" class="btn btn-outline-secondary btn-sm me-2">Page Content</a>
                                <a href="{{ route('dashboard.visa-nationalities.create') }}" class="btn btn-primary btn-sm">Add Nationality</a>
                            </div>
                        </div>
                        <div class="card-body order-datatable overflow-x-auto">
                            {!! $dataTable->table(['class'=>'display']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

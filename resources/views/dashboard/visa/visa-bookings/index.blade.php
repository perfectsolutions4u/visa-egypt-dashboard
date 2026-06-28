@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Visa Bookings">
            <li class="breadcrumb-item active">Visa Bookings</li>
        </x-dashboard.partials.breadcrumb>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="GET" class="row g-2">
                                <div class="col-md-2">
                                    <select name="service_type" class="form-control">
                                        <option value="">All Services</option>
                                        @foreach(\App\Enums\Visa\VisaServiceType::cases() as $type)
                                            <option value="{{ $type->value }}" @selected(request('service_type')==$type->value)>{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->value }}" @selected(request('status')==$status->value)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
                                <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
                                <div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h5>Visa Egypt Bookings</h5></div>
                        <div class="card-body order-datatable overflow-x-auto">
                            {!! $dataTable->table(['class'=>'display']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

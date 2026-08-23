@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Additional Services">
            <li class="breadcrumb-item active">Additional Services</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert/>
                    <div class="alert alert-info">
                        These services appear in the mobile app on the
                        <strong>Add Additional Services</strong> screen.
                        Inactive services are hidden from the app.
                    </div>
                    <div class="card">
                        <x-dashboard.partials.table-card-header model="additional-service"/>
                        <div class="card-body order-datatable overflow-x-auto">
                            {!! $dataTable->table(['class'=>'display']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

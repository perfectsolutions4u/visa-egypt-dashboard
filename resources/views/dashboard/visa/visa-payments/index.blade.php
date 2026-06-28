@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Visa Payments">
            <li class="breadcrumb-item active">Visa Payments</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert/>
                    <div class="card">
                        <div class="card-header">
                            <form class="form-inline search-form search-box">
                                <div class="form-group">
                                    <input id="datatable-search" aria-label="Search" class="form-control" type="search" placeholder="Search..">
                                </div>
                            </form>
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

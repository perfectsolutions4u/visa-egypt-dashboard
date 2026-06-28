@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Offers">
            <li class="breadcrumb-item active">Offers</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert/>
                    <div class="card">
                        <x-dashboard.partials.table-card-header model="offer"/>
                        <div class="card-body order-datatable overflow-x-auto">
                            {!! $dataTable->table(['class'=>'display']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

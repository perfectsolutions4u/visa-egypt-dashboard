@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.visa-nationalities.update', $nationality) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Nationality" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.visa-nationalities.index') }}">Eligible Nationalities</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="name" name="name" id="name" label-title="Name" :value="$nationality->name"/>
                        <x-dashboard.form.input-text error-key="code" name="code" id="code" label-title="Code (e.g. US)" :value="$nationality->code"/>
                        <x-dashboard.form.input-text error-key="aliases" name="aliases" id="aliases" label-title="Aliases (comma separated)" :value="$nationality->aliases"/>
                        <x-dashboard.form.input-text error-key="sort_order" name="sort_order" id="sort_order" label-title="Sort Order" :value="$nationality->sort_order"/>
                        <x-dashboard.form.input-checkbox resource-name="Nationality" error-key="is_active" :value="$nationality->is_active" name="is_active" id="is_active" label-title="Active"/>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

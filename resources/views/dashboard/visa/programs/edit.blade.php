@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.programs.update', $program) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Edit Program" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.programs.index') }}">Programs</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="name" name="name" :value="$program->name" id="name" label-title="Name"/>
                        <x-dashboard.form.input-text error-key="slug" name="slug" :value="$program->slug" id="slug" label-title="Slug"/>
                        <x-dashboard.form.input-text error-key="duration" name="duration" :value="$program->duration" id="duration" label-title="Duration"/>
                        <x-dashboard.form.input-text error-key="starting_price" name="starting_price" :value="$program->starting_price" id="starting_price" label-title="Starting Price"/>
                        <x-dashboard.form.input-text error-key="hero_image" name="hero_image" :value="$program->hero_image" id="hero_image" label-title="Hero Image URL"/>
                        <x-dashboard.form.input-text error-key="sort_order" name="sort_order" :value="$program->sort_order" id="sort_order" label-title="Sort Order"/>
                        <x-dashboard.form.input-checkbox resource-name="Program" error-key="is_active" :value="$program->is_active" name="is_active" id="is_active" label-title="Active"/>
                        <x-dashboard.form.input-checkbox resource-name="Program" error-key="is_best_seller" :value="$program->is_best_seller" name="is_best_seller" id="is_best_seller" label-title="Best Seller"/>

                        <x-dashboard.form.input-textarea error-key="cities" name="cities" id="cities" label-title="Cities (JSON)"
                            :value="$program->cities ? json_encode($program->cities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ''"/>
                        <x-dashboard.form.input-textarea error-key="highlights" name="highlights" id="highlights" label-title="Highlights (JSON)"
                            :value="$program->highlights ? json_encode($program->highlights, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ''"/>
                        <x-dashboard.form.input-textarea error-key="itinerary" name="itinerary" id="itinerary" label-title="Itinerary (JSON)"
                            :value="$program->itinerary ? json_encode($program->itinerary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ''"/>
                        <x-dashboard.form.input-textarea error-key="inclusions" name="inclusions" id="inclusions" label-title="Inclusions (JSON)"
                            :value="$program->inclusions ? json_encode($program->inclusions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ''"/>
                        <x-dashboard.form.input-textarea error-key="exclusions" name="exclusions" id="exclusions" label-title="Exclusions (JSON)"
                            :value="$program->exclusions ? json_encode($program->exclusions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ''"/>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

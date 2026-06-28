@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.policies.update') }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Mobile Policies" :hideFirst="true">
            <li class="breadcrumb-item active">Policies</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>App Policies Page</h5>
                            <span class="text-muted">Shown in the Visa Egypt mobile app under Profile → Policies.</span>
                        </div>
                        <div class="card-body">
                            <x-dashboard.form.input-textarea
                                error-key="terms"
                                name="terms"
                                id="terms"
                                label-title="Terms of Service"
                                :value="old('terms', $content['terms'])"
                            />

                            <x-dashboard.form.input-textarea
                                error-key="privacy"
                                name="privacy"
                                id="privacy"
                                label-title="Privacy Policy"
                                :value="old('privacy', $content['privacy'])"
                            />

                            <x-dashboard.form.input-textarea
                                error-key="about"
                                name="about"
                                id="about"
                                label-title="About Us"
                                :value="old('about', $content['about'])"
                            />
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save Policies</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

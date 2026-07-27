@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.app-update-settings.update') }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="App Update" :hideFirst="true">
            <li class="breadcrumb-item active">Settings</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-12 col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Update Control</h5>
                            <span class="text-muted">Dashboard is the source of truth for mobile version checks and store links.</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="is_active"
                                        name="is_active"
                                        value="1"
                                        @checked(old('is_active', $settings['is_active']))
                                    >
                                    <label class="form-check-label" for="is_active">Enable app update checks</label>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="force_update" value="0">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="force_update"
                                        name="force_update"
                                        value="1"
                                        @checked(old('force_update', $settings['force_update']))
                                    >
                                    <label class="form-check-label" for="force_update">Force update (no Later button)</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <x-dashboard.form.input-text
                                        error-key="latest_version"
                                        required
                                        :value="old('latest_version', $settings['latest_version'])"
                                        name="latest_version"
                                        label-title="Latest version"
                                        placeholder="1.0.2"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-dashboard.form.input-text
                                        error-key="min_version"
                                        :value="old('min_version', $settings['min_version'])"
                                        name="min_version"
                                        label-title="Minimum allowed version"
                                        placeholder="1.0.0"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Android</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-dashboard.form.input-text
                                        error-key="android_version"
                                        :value="old('android_version', $settings['android_version'])"
                                        name="android_version"
                                        label-title="Android version"
                                        placeholder="1.0.2"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-dashboard.form.input-text
                                        error-key="android_build"
                                        required
                                        type="number"
                                        min="1"
                                        :value="old('android_build', $settings['android_build'])"
                                        name="android_build"
                                        label-title="Android build number"
                                    />
                                </div>
                            </div>
                            <x-dashboard.form.input-text
                                error-key="android_download_url"
                                :value="old('android_download_url', $settings['android_download_url'])"
                                name="android_download_url"
                                label-title="Android download URL"
                                placeholder="https://play.google.com/store/apps/details?id=..."
                            />
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>iOS</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-dashboard.form.input-text
                                        error-key="ios_version"
                                        :value="old('ios_version', $settings['ios_version'])"
                                        name="ios_version"
                                        label-title="iOS version"
                                        placeholder="1.0.2"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-dashboard.form.input-text
                                        error-key="ios_build"
                                        required
                                        type="number"
                                        min="1"
                                        :value="old('ios_build', $settings['ios_build'])"
                                        name="ios_build"
                                        label-title="iOS build number"
                                    />
                                </div>
                            </div>
                            <x-dashboard.form.input-text
                                error-key="ios_download_url"
                                :value="old('ios_download_url', $settings['ios_download_url'])"
                                name="ios_download_url"
                                label-title="iOS download URL"
                                placeholder="https://apps.apple.com/app/id..."
                            />
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Update Message</h5>
                        </div>
                        <div class="card-body">
                            <x-dashboard.form.input-text
                                error-key="message_en"
                                :value="old('message_en', $settings['message_en'])"
                                name="message_en"
                                label-title="Message (English)"
                            />
                            <x-dashboard.form.input-text
                                error-key="message_ar"
                                :value="old('message_ar', $settings['message_ar'])"
                                name="message_ar"
                                label-title="Message (Arabic)"
                            />
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Mobile API</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2 text-muted">Public endpoint used by the app splash:</p>
                            <code class="d-block mb-3">GET /api/v1/settings/app-update</code>
                            <p class="mb-2 text-muted">Optional decision helper:</p>
                            <code class="d-block">?platform=android&amp;version=1.0.0&amp;build=1</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

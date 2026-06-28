@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.app-notifications.store') }}" method="POST" class="page-body">
        @csrf

        <x-dashboard.partials.breadcrumb title="Send Notification" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.app-notifications.index') }}">App Notifications</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-checkbox resource-name="Notification" resource-desc="Send to all clients" error-key="broadcast_all" name="broadcast_all" id="broadcast_all" label-title="Broadcast to All"/>
                        <x-dashboard.form.input-select :options="$clients" error-key="client_id" name="client_id" id="client_id" label-title="Client (if not broadcasting)"/>
                        <x-dashboard.form.input-text error-key="title" name="title" id="title" label-title="Title"/>
                        <x-dashboard.form.input-textarea error-key="body" name="body" id="body" label-title="Body"/>
                        <x-dashboard.form.input-text error-key="type" name="type" id="type" label-title="Type"/>
                        <x-dashboard.form.input-text error-key="target_screen" name="target_screen" id="target_screen" label-title="Target Screen"/>
                        <x-dashboard.form.input-text error-key="target_id" name="target_id" id="target_id" label-title="Target ID"/>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            const toggleClient = () => {
                const disabled = $('#broadcast-all').is(':checked');
                $('#client-id').prop('disabled', disabled);
                if (disabled) {
                    $('#client-id').val('');
                }
            };

            $('#broadcast-all').on('change', toggleClient);
            toggleClient();
        });
    </script>
@endpush

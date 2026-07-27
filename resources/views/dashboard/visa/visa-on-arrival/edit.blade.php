@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.visa-on-arrival.update') }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Visa On Arrival" :hideFirst="true">
            <li class="breadcrumb-item active">Page Content</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-12 mb-3">
                    <a href="{{ route('dashboard.visa-nationalities.index') }}" class="btn btn-outline-primary">
                        Manage Eligible Nationalities
                    </a>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Visa On Arrival Page</h5>
                            <span class="text-muted">Shown in the Visa Egypt mobile app under Visa On Arrival.</span>
                        </div>
                        <div class="card-body">
                            <x-dashboard.form.input-text
                                error-key="title"
                                name="title"
                                id="title"
                                label-title="Title"
                                :value="old('title', $content['title'])"
                            />

                            <x-dashboard.form.input-textarea
                                error-key="subtitle"
                                name="subtitle"
                                id="subtitle"
                                label-title="Subtitle"
                                :value="old('subtitle', $content['subtitle'])"
                            />

                            <x-dashboard.form.input-text
                                error-key="visa_fee_usd"
                                name="visa_fee_usd"
                                id="visa_fee_usd"
                                label-title="Visa Fee (USD)"
                                :value="old('visa_fee_usd', $content['visa_fee_usd'])"
                            />

                            <x-dashboard.form.input-text
                                error-key="stay_days"
                                name="stay_days"
                                id="stay_days"
                                label-title="Stay Days"
                                :value="old('stay_days', $content['stay_days'])"
                            />

                            <x-dashboard.form.input-text
                                error-key="entry_type"
                                name="entry_type"
                                id="entry_type"
                                label-title="Entry Type"
                                :value="old('entry_type', $content['entry_type'])"
                            />

                            <x-dashboard.form.input-textarea
                                error-key="eligible_message"
                                name="eligible_message"
                                id="eligible_message"
                                label-title="Eligible Message"
                                :value="old('eligible_message', $content['eligible_message'])"
                            />

                            <x-dashboard.form.input-textarea
                                error-key="ineligible_message"
                                name="ineligible_message"
                                id="ineligible_message"
                                label-title="Ineligible Message"
                                :value="old('ineligible_message', $content['ineligible_message'])"
                            />

                            <x-dashboard.form.input-title-description-list
                                name="features"
                                error-key="features"
                                label-title="Features"
                                add-label="Add Feature"
                                :items="$content['features']"
                            />

                            <x-dashboard.form.input-title-description-list
                                name="required_documents"
                                error-key="required_documents"
                                label-title="Required Documents"
                                add-label="Add Document"
                                :items="$content['required_documents']"
                            />

                            <x-dashboard.form.input-title-description-list
                                name="steps"
                                error-key="steps"
                                label-title="How It Works Steps"
                                add-label="Add Step"
                                :items="$content['steps']"
                            />
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save Page Content</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
<script>
(function () {
    function reindexRows(list) {
        list.querySelectorAll('.visa-item-row').forEach(function (row, index) {
            row.querySelector('.visa-item-index').textContent = 'Item ' + (index + 1);
            row.querySelectorAll('input, textarea').forEach(function (input) {
                const name = input.getAttribute('name') || '';
                input.setAttribute(
                    'name',
                    name.replace(/\[\d+]/, '[' + index + ']')
                );
            });
        });
    }

    function createRow(fieldName, index) {
        const wrap = document.createElement('div');
        wrap.className = 'visa-item-row border rounded p-3 mb-2 bg-light';
        wrap.innerHTML =
            '<div class="d-flex justify-content-between align-items-center mb-2">' +
                '<strong class="visa-item-index">Item ' + (index + 1) + '</strong>' +
                '<button type="button" class="btn btn-sm btn-outline-danger visa-item-remove">' +
                    '<i class="fa fa-trash"></i> Remove' +
                '</button>' +
            '</div>' +
            '<div class="mb-2">' +
                '<label class="form-label mb-1">Title</label>' +
                '<input type="text" class="form-control" name="' + fieldName + '[' + index + '][title]" value="" placeholder="Title">' +
            '</div>' +
            '<div>' +
                '<label class="form-label mb-1">Description</label>' +
                '<textarea class="form-control" name="' + fieldName + '[' + index + '][description]" rows="2" placeholder="Description"></textarea>' +
            '</div>';
        return wrap;
    }

    document.querySelectorAll('.visa-item-list').forEach(function (list) {
        const fieldName = list.getAttribute('data-field');
        const rowsWrap = list.querySelector('.visa-item-rows');

        list.querySelector('.visa-item-add').addEventListener('click', function () {
            const index = rowsWrap.querySelectorAll('.visa-item-row').length;
            rowsWrap.appendChild(createRow(fieldName, index));
        });

        list.addEventListener('click', function (event) {
            const button = event.target.closest('.visa-item-remove');
            if (!button) return;

            const rows = rowsWrap.querySelectorAll('.visa-item-row');
            if (rows.length <= 1) {
                const only = rows[0];
                only.querySelectorAll('input, textarea').forEach(function (input) {
                    input.value = '';
                });
                return;
            }

            button.closest('.visa-item-row').remove();
            reindexRows(list);
        });
    });
})();
</script>
@endpush

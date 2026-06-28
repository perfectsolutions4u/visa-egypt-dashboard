@php
    $voucher = $voucher ?? null;
    $discountTypes = [
        'fixed' => 'Fixed Amount (USD)',
        'percentage' => 'Percentage (%)',
    ];
    $serviceTargets = ['' => 'All Services'] + collect(\App\Enums\Visa\OfferServiceTarget::cases())
        ->mapWithKeys(fn ($target) => [$target->value => Str::headline($target->value)])
        ->all();
@endphp

<x-dashboard.form.input-text error-key="code" name="code" :value="old('code', $voucher?->code)" id="code" label-title="Voucher Code (e.g. WELCOME50)"/>
<x-dashboard.form.input-text error-key="title" name="title" :value="old('title', $voucher?->title)" id="title" label-title="Title"/>
<x-dashboard.form.input-textarea error-key="description" name="description" id="description" label-title="Description" :value="old('description', $voucher?->description)"/>
<x-dashboard.form.input-select :options="$discountTypes" :value="old('discount_type', $voucher?->discount_type?->value ?? 'fixed')" error-key="discount_type" name="discount_type" id="discount_type" label-title="Discount Type"/>
<x-dashboard.form.input-text error-key="discount_value" name="discount_value" :value="old('discount_value', $voucher?->discount_value ?? 10)" id="discount_value" label-title="Discount Value"/>
<x-dashboard.form.input-text error-key="min_amount" name="min_amount" :value="old('min_amount', $voucher?->min_amount)" id="min_amount" label-title="Minimum Order Amount (USD, optional)"/>
<x-dashboard.form.input-select :options="$serviceTargets" :value="old('service_target', $voucher?->service_target)" error-key="service_target" name="service_target" id="service_target" label-title="Service Target"/>
<x-dashboard.form.input-select :options="$clients" :value="old('client_id', $voucher?->client_id)" error-key="client_id" name="client_id" id="client_id" label-title="Assigned Client"/>
<x-dashboard.form.input-text error-key="max_uses" name="max_uses" :value="old('max_uses', $voucher?->max_uses)" id="max_uses" label-title="Maximum Uses (leave empty for unlimited)"/>
@if($voucher)
    <x-dashboard.form.input-text error-key="used_count" name="used_count" :value="$voucher->used_count" id="used_count" label-title="Times Used" :disabled="true"/>
@endif
<x-dashboard.form.input-text class="input-datepicker allow-past" error-key="valid_from" name="valid_from" :value="old('valid_from', optional($voucher?->valid_from)->format('Y-m-d'))" id="valid_from" label-title="Valid From"/>
<x-dashboard.form.input-text class="input-datepicker allow-past" error-key="valid_to" name="valid_to" :value="old('valid_to', optional($voucher?->valid_to)->format('Y-m-d'))" id="valid_to" label-title="Valid To"/>
<x-dashboard.form.input-checkbox resource-name="Voucher" error-key="is_active" :value="old('is_active', $voucher?->is_active ?? true)" name="is_active" id="is_active" label-title="Active"/>

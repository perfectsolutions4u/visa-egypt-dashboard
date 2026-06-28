@php
    $membership = $membership ?? null;
    $membershipPlans = \App\Models\Visa\MembershipTier::optionsForSelect();
    if ($membershipPlans === []) {
        $membershipPlans = collect(\App\Enums\Visa\MembershipPlan::cases())
            ->mapWithKeys(fn ($plan) => [$plan->value => Str::headline($plan->value)])
            ->all();
    }
    $statuses = [
        'pending' => 'Pending',
        'active' => 'Active',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
    ];
    $planDiscounts = \App\Models\Visa\MembershipTier::discountMap();
    if ($planDiscounts === []) {
        $planDiscounts = collect(\App\Enums\Visa\MembershipPlan::cases())
            ->mapWithKeys(fn ($plan) => [$plan->value => $plan->discountPercent()])
            ->all();
    }
    $defaultPlan = array_key_first($membershipPlans) ?: 'silver';
@endphp

<x-dashboard.form.input-select :options="$clients" :value="old('client_id', $membership?->client_id)" error-key="client_id" name="client_id" id="client_id" label-title="Client"/>
<x-dashboard.form.input-select :options="$membershipPlans" :value="old('plan_type', $membership?->plan_type ?? $defaultPlan)" error-key="plan_type" name="plan_type" id="plan_type" label-title="Plan Type"/>
<x-dashboard.form.input-text error-key="discount_percent" name="discount_percent" :value="old('discount_percent', $membership?->discount_percent ?? ($planDiscounts[$defaultPlan] ?? 10))" id="discount_percent" label-title="Discount Percent"/>
<x-dashboard.form.input-text error-key="points_balance" name="points_balance" :value="old('points_balance', $membership?->points_balance ?? 0)" id="points_balance" label-title="Points Balance"/>
<x-dashboard.form.input-text class="input-datepicker" error-key="start_date" name="start_date" :value="old('start_date', optional($membership?->start_date)->format('Y-m-d'))" id="start_date" label-title="Start Date"/>
<x-dashboard.form.input-text class="input-datepicker" error-key="end_date" name="end_date" :value="old('end_date', optional($membership?->end_date)->format('Y-m-d'))" id="end_date" label-title="End Date"/>
<x-dashboard.form.input-select :options="$statuses" :value="old('status', $membership?->status ?? 'active')" error-key="status" name="status" id="status" label-title="Status"/>

@push('js')
<script>
    (function () {
        const discounts = @json($planDiscounts);
        const planSelect = document.getElementById('plan_type');
        const discountInput = document.getElementById('discount_percent');
        if (!planSelect || !discountInput) return;

        planSelect.addEventListener('change', function () {
            if (discounts[this.value] !== undefined) {
                discountInput.value = discounts[this.value];
            }
        });
    })();
</script>
@endpush

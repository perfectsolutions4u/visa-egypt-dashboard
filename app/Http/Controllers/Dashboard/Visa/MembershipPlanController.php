<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\MembershipPlanDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\MembershipPlanRequest;
use App\Models\Coupon;
use App\Models\Visa\Membership;
use App\Models\Visa\MembershipTier;
use App\Models\Visa\Voucher;

class MembershipPlanController extends Controller
{
    public function index(MembershipPlanDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.membership-plans.index');
    }

    public function manage()
    {
        $plans = MembershipTier::query()
            ->with(['vouchers', 'coupons'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $stats = [
            'total' => $plans->count(),
            'active' => $plans->where('is_active', true)->count(),
            'featured' => $plans->firstWhere('is_featured', true)?->name,
        ];

        return view('dashboard.visa.membership-plans.manage', compact('plans', 'stats'));
    }

    public function toggleActive(MembershipTier $membershipPlan)
    {
        $membershipPlan->update(['is_active' => ! $membershipPlan->is_active]);
        session()->flash('message', 'Plan status updated.');
        session()->flash('type', 'success');

        return back();
    }

    public function toggleFeatured(MembershipTier $membershipPlan)
    {
        if (! $membershipPlan->is_featured) {
            MembershipTier::query()
                ->where('id', '!=', $membershipPlan->id)
                ->update(['is_featured' => false]);
        }

        $membershipPlan->update(['is_featured' => ! $membershipPlan->is_featured]);
        session()->flash('message', 'Featured plan updated.');
        session()->flash('type', 'success');

        return back();
    }

    public function create()
    {
        return view('dashboard.visa.membership-plans.create', $this->formOptions());
    }

    public function store(MembershipPlanRequest $request)
    {
        $plan = MembershipTier::create($request->getSanitized());
        $this->syncBenefits($plan, $request);
        session()->flash('message', 'Membership Plan Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.membership-plans.edit', $plan);
    }

    public function edit(MembershipTier $membershipPlan)
    {
        $membershipPlan->load(['vouchers', 'coupons']);

        return view('dashboard.visa.membership-plans.edit', array_merge(
            $this->formOptions($membershipPlan),
            ['plan' => $membershipPlan]
        ));
    }

    public function update(MembershipPlanRequest $request, MembershipTier $membershipPlan)
    {
        $membershipPlan->update($request->getSanitized());
        $this->syncBenefits($membershipPlan, $request);
        session()->flash('message', 'Membership Plan Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(MembershipTier $membershipPlan)
    {
        $inUse = Membership::query()->where('plan_type', $membershipPlan->slug)->exists();

        if ($inUse) {
            return response()->json([
                'message' => 'This plan is assigned to clients and cannot be deleted. Deactivate it instead.',
            ], 422);
        }

        $membershipPlan->delete();

        return response()->json([
            'message' => 'Membership Plan Deleted Successfully!',
        ]);
    }

    private function formOptions(?MembershipTier $plan = null): array
    {
        return [
            'vouchers' => Voucher::query()->orderBy('title')->orderBy('code')->get(),
            'coupons' => Coupon::query()->orderBy('title')->orderBy('code')->get(),
            'selectedVoucherIds' => collect(old('voucher_ids', $plan?->vouchers->pluck('id')->all() ?? []))
                ->map(fn ($id) => (int) $id)
                ->all(),
            'selectedCouponIds' => collect(old('coupon_ids', $plan?->coupons->pluck('id')->all() ?? []))
                ->map(fn ($id) => (int) $id)
                ->all(),
        ];
    }

    private function syncBenefits(MembershipTier $plan, MembershipPlanRequest $request): void
    {
        $plan->vouchers()->sync($request->input('voucher_ids', []));
        $plan->coupons()->sync($request->input('coupon_ids', []));
    }
}

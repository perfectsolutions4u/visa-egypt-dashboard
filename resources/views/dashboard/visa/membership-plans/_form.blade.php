@php
    $plan = $plan ?? null;
    $featuresText = old(
        'features',
        is_array($plan?->features) ? implode("\n", $plan->features) : ''
    );
    $vouchers = $vouchers ?? collect();
    $coupons = $coupons ?? collect();
    $selectedVoucherIds = $selectedVoucherIds ?? [];
    $selectedCouponIds = $selectedCouponIds ?? [];
@endphp

<x-dashboard.form.input-text error-key="slug" name="slug" :value="old('slug', $plan?->slug)" id="slug" label-title="Slug (e.g. basic, comfort, premium, vip)"/>
<x-dashboard.form.input-text error-key="name" name="name" :value="old('name', $plan?->name)" id="name" label-title="Display Name"/>
<x-dashboard.form.input-text error-key="tagline" name="tagline" :value="old('tagline', $plan?->tagline)" id="tagline" label-title="Tagline (e.g. Essential Support)"/>
<x-dashboard.form.input-textarea error-key="description" name="description" id="description" label-title="Description" :value="old('description', $plan?->description)"/>
<x-dashboard.form.input-textarea error-key="features" name="features" id="features" label-title="Features (one per line)" :value="$featuresText"/>
<x-dashboard.form.input-text error-key="special_offer_text" name="special_offer_text" :value="old('special_offer_text', $plan?->special_offer_text)" id="special_offer_text" label-title="Special Offer Text (e.g. Visa On Arrival Included (30 USD))"/>
<x-dashboard.form.input-checkbox resource-name="Membership Plan" error-key="special_offer_included" :value="old('special_offer_included', $plan?->special_offer_included ?? false)" name="special_offer_included" id="special_offer_included" label-title="Special Offer Included (green check)"/>
<x-dashboard.form.input-text error-key="theme_color" name="theme_color" :value="old('theme_color', $plan?->theme_color ?? '#007BFF')" id="theme_color" label-title="Theme Color (hex, e.g. #6F42C1)"/>
<x-dashboard.form.input-checkbox resource-name="Membership Plan" error-key="is_featured" :value="old('is_featured', $plan?->is_featured ?? false)" name="is_featured" id="is_featured" label-title="Featured Plan (highlighted in app)"/>
<x-dashboard.form.input-text error-key="discount_percent" name="discount_percent" :value="old('discount_percent', $plan?->discount_percent ?? 10)" id="discount_percent" label-title="Discount Percent"/>
<x-dashboard.form.input-text error-key="price_usd" name="price_usd" :value="old('price_usd', $plan?->price_usd ?? 49)" id="price_usd" label-title="Price (USD)"/>
<x-dashboard.form.input-text error-key="daily_points" name="daily_points" type="number" min="0" :value="old('daily_points', $plan?->daily_points ?? 0)" id="daily_points" label-title="Daily Reward Points (for subscribers)"/>
<x-dashboard.form.input-text error-key="sort_order" name="sort_order" :value="old('sort_order', $plan?->sort_order ?? 0)" id="sort_order" label-title="Sort Order"/>
<x-dashboard.form.input-checkbox resource-name="Membership Plan" error-key="is_active" :value="old('is_active', $plan?->is_active ?? true)" name="is_active" id="is_active" label-title="Active"/>

<hr>
<h6 class="mb-3">Special Vouchers & Coupons</h6>
<p class="text-muted small">Select vouchers and coupons included with this membership plan. Members will see these perks in the app.</p>

<div class="form-group row">
    <label class="col-xl-3 col-md-4">Included Vouchers</label>
    <div class="col-xl-8 col-md-7">
        @forelse($vouchers as $voucher)
            <div class="checkbox checkbox-primary mb-2">
                <input
                    type="checkbox"
                    id="voucher-{{ $voucher->id }}"
                    name="voucher_ids[]"
                    value="{{ $voucher->id }}"
                    @checked(in_array($voucher->id, $selectedVoucherIds, true))
                >
                <label for="voucher-{{ $voucher->id }}">
                    <strong>{{ $voucher->code }}</strong> — {{ $voucher->title }}
                    @unless($voucher->is_active)
                        <span class="badge bg-secondary">Inactive</span>
                    @endunless
                </label>
            </div>
        @empty
            <p class="text-muted mb-0">No vouchers yet. <a href="{{ route('dashboard.vouchers.create') }}">Create a voucher</a> first.</p>
        @endforelse
        @error('voucher_ids')
            <span class="d-block text-danger">{{ $message }}</span>
        @enderror
        @error('voucher_ids.*')
            <span class="d-block text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label class="col-xl-3 col-md-4">Included Coupons</label>
    <div class="col-xl-8 col-md-7">
        @forelse($coupons as $coupon)
            <div class="checkbox checkbox-primary mb-2">
                <input
                    type="checkbox"
                    id="coupon-{{ $coupon->id }}"
                    name="coupon_ids[]"
                    value="{{ $coupon->id }}"
                    @checked(in_array($coupon->id, $selectedCouponIds, true))
                >
                <label for="coupon-{{ $coupon->id }}">
                    <strong>{{ $coupon->code }}</strong> — {{ $coupon->title }}
                    @unless($coupon->active)
                        <span class="badge bg-secondary">Inactive</span>
                    @endunless
                </label>
            </div>
        @empty
            <p class="text-muted mb-0">No coupons yet. <a href="{{ route('dashboard.coupons.create') }}">Create a coupon</a> first.</p>
        @endforelse
        @error('coupon_ids')
            <span class="d-block text-danger">{{ $message }}</span>
        @enderror
        @error('coupon_ids.*')
            <span class="d-block text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

<?php

namespace App\Services\Visa;

use App\Enums\CouponType;
use App\Models\Client;
use App\Models\Coupon;
use App\Models\Visa\ClientVoucher;
use App\Models\Visa\MembershipTier;
use App\Models\Visa\VisaPayment;
use App\Models\Visa\Voucher;
use Illuminate\Validation\ValidationException;

class VisaCouponService
{
    public function find(?int $id, ?string $code): ?Coupon
    {
        if ($id) {
            return Coupon::query()->find($id);
        }

        if ($code) {
            return Coupon::query()
                ->where('code', strtoupper(trim($code)))
                ->first();
        }

        return null;
    }

    public function validateForPayment(Client $client, Coupon $coupon, float $subtotal): void
    {
        if (! $coupon->active) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon is not active.'],
            ]);
        }

        if ($coupon->expired()) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon has expired.'],
            ]);
        }

        if ($coupon->limit_per_usage) {
            $usage = VisaPayment::query()->where('coupon_id', $coupon->id)->count();
            if ($usage >= $coupon->limit_per_usage) {
                throw ValidationException::withMessages([
                    'coupon_code' => ['This coupon has reached its usage limit.'],
                ]);
            }
        }

        if ($coupon->limit_per_customer) {
            $usage = VisaPayment::query()
                ->where('coupon_id', $coupon->id)
                ->where('client_id', $client->id)
                ->count();

            if ($usage >= $coupon->limit_per_customer) {
                throw ValidationException::withMessages([
                    'coupon_code' => ['You have already used this coupon.'],
                ]);
            }
        }

        if ($subtotal <= 0) {
            throw ValidationException::withMessages([
                'subtotal' => ['Invalid order amount for coupon.'],
            ]);
        }
    }

    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        $discounted = (float) $coupon->apply($subtotal);

        return round(max(0, min($subtotal, $subtotal - $discounted)), 2);
    }

    public function membershipCoupons(Client $client): array
    {
        $membership = $client->activeMembership;
        if (! $membership) {
            return [];
        }

        $tier = MembershipTier::query()
            ->with('coupons')
            ->where('slug', $membership->plan_type)
            ->first();

        if (! $tier) {
            return [];
        }

        return $tier->coupons
            ->where('active', true)
            ->filter(fn (Coupon $coupon) => ! $coupon->expired())
            ->values()
            ->all();
    }
}

<?php

namespace App\Services\Visa;

use App\Models\Client;
use App\Models\Coupon;
use App\Models\Visa\VisaPayment;
use App\Models\Visa\Voucher;
use Illuminate\Validation\ValidationException;

class PaymentDiscountService
{
    public function __construct(
        private readonly VisaCouponService $coupons,
        private readonly VisaVoucherPaymentService $vouchers,
        private readonly LoyaltyService $loyalty,
        private readonly WalletService $wallets
    ) {}

    public function options(Client $client, float $subtotal, ?string $serviceType = null): array
    {
        $loyalty = $this->loyalty->getSettings();
        $balance = $this->loyalty->getBalance($client);
        $hasMembership = $this->loyalty->activeMembership($client) !== null;

        $walletVouchers = $client->vouchers()->get();
        $usedVoucherIds = VisaPayment::query()
            ->where('client_id', $client->id)
            ->whereNotNull('voucher_id')
            ->pluck('voucher_id')
            ->all();

        $vouchers = $walletVouchers
            ->reject(fn (Voucher $voucher) => in_array($voucher->id, $usedVoucherIds, true))
            ->filter(fn (Voucher $voucher) => $this->isVoucherEligible($voucher, $subtotal, $serviceType))
            ->values();

        $membershipCoupons = collect($this->coupons->membershipCoupons($client))
            ->filter(fn (Coupon $coupon) => $this->isCouponEligible($client, $coupon, $subtotal))
            ->values();

        return [
            'subtotal' => round($subtotal, 2),
            'service_type' => $serviceType,
            'vouchers' => $vouchers->map(fn (Voucher $v) => $this->voucherPayload($v, $subtotal))->all(),
            'coupons' => $membershipCoupons->map(fn (Coupon $c) => $this->couponPayload($c, $subtotal))->all(),
            'loyalty' => [
                'enabled' => (bool) $loyalty['enabled'],
                'points_balance' => $balance,
                'has_membership' => $hasMembership,
                'min_points_redeem' => (int) $loyalty['min_points_redeem'],
                'redeem_points_per_usd' => (int) $loyalty['redeem_points_per_usd'],
                'max_redeem_percent' => (float) $loyalty['max_redeem_percent'],
            ],
            'wallet' => [
                'balance' => $this->wallets->getBalance($client),
                'max_usable' => round(min($this->wallets->getBalance($client), $subtotal), 2),
                'currency' => 'USD',
            ],
        ];
    }

    public function preview(Client $client, float $subtotal, array $input, ?string $serviceType = null): array
    {
        $discountType = $input['discount_type'] ?? null;
        $couponId = $input['coupon_id'] ?? null;
        $couponCode = $input['coupon_code'] ?? null;
        $voucherId = $input['voucher_id'] ?? null;
        $pointsToUse = (int) ($input['points_to_use'] ?? 0);
        $walletAmountToUse = isset($input['wallet_amount_to_use'])
            ? (float) $input['wallet_amount_to_use']
            : null;

        $choices = array_filter([
            $discountType === 'coupon' || $couponId || $couponCode,
            $discountType === 'voucher' || $voucherId,
            $discountType === 'points' || $pointsToUse > 0,
            $discountType === 'wallet' || ($walletAmountToUse !== null && $walletAmountToUse > 0),
        ]);

        if (count($choices) > 1) {
            throw ValidationException::withMessages([
                'discount_type' => ['Choose only one: coupon, voucher, points, or wallet.'],
            ]);
        }

        $base = [
            'discount_type' => null,
            'coupon_id' => null,
            'coupon_code' => null,
            'voucher_id' => null,
            'points_to_use' => 0,
            'wallet_amount_to_use' => 0.0,
            'discount_amount' => 0.0,
            'loyalty_discount' => 0.0,
            'subtotal' => round($subtotal, 2),
            'total' => round($subtotal, 2),
            'points_to_earn' => 0,
            'points_balance' => $this->loyalty->getBalance($client),
            'wallet_balance' => $this->wallets->getBalance($client),
        ];

        if ($discountType === 'coupon' || $couponId || $couponCode) {
            return $this->previewCoupon($client, $subtotal, $couponId, $couponCode, $base);
        }

        if ($discountType === 'voucher' || $voucherId) {
            return $this->previewVoucher($client, $subtotal, (int) $voucherId, $serviceType, $base);
        }

        if ($discountType === 'points' || $pointsToUse > 0) {
            return $this->previewPoints($client, $subtotal, $pointsToUse, $base);
        }

        if ($discountType === 'wallet' || ($walletAmountToUse !== null && $walletAmountToUse > 0)) {
            return $this->previewWallet($client, $subtotal, $walletAmountToUse, $base);
        }

        return $this->withEarnOnly($client, $subtotal, $base);
    }

    public function applyForPayment(Client $client, float $subtotal, array $input, ?string $serviceType = null): array
    {
        return $this->preview($client, $subtotal, $input, $serviceType);
    }

    private function previewCoupon(Client $client, float $subtotal, ?int $couponId, ?string $couponCode, array $base): array
    {
        $coupon = $this->coupons->find($couponId, $couponCode);

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => ['Invalid coupon code.'],
            ]);
        }

        $this->coupons->validateForPayment($client, $coupon, $subtotal);
        $discount = $this->coupons->calculateDiscount($coupon, $subtotal);
        $total = max(0.01, round($subtotal - $discount, 2));

        return $this->withEarnOnly($client, $total, array_merge($base, [
            'discount_type' => 'coupon',
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
            'loyalty_discount' => $discount,
            'total' => $total,
        ]));
    }

    private function previewVoucher(
        Client $client,
        float $subtotal,
        int $voucherId,
        ?string $serviceType,
        array $base
    ): array {
        if ($voucherId <= 0) {
            throw ValidationException::withMessages([
                'voucher_id' => ['Please select a voucher.'],
            ]);
        }

        $voucher = $this->vouchers->findInWallet($client, $voucherId);

        if (! $voucher) {
            throw ValidationException::withMessages([
                'voucher_id' => ['Voucher not found in your wallet.'],
            ]);
        }

        $this->vouchers->validateForPayment($client, $voucher, $subtotal, $serviceType);
        $discount = $this->vouchers->calculateDiscount($voucher, $subtotal);
        $total = max(0.01, round($subtotal - $discount, 2));

        return $this->withEarnOnly($client, $total, array_merge($base, [
            'discount_type' => 'voucher',
            'voucher_id' => $voucher->id,
            'discount_amount' => $discount,
            'loyalty_discount' => $discount,
            'total' => $total,
        ]));
    }

    private function previewPoints(Client $client, float $subtotal, int $pointsToUse, array $base): array
    {
        $loyaltyPreview = $this->loyalty->applyToPayment($client, $subtotal, $pointsToUse);

        return array_merge($base, [
            'discount_type' => 'points',
            'points_to_use' => $loyaltyPreview['points_to_use'],
            'discount_amount' => $loyaltyPreview['loyalty_discount'],
            'loyalty_discount' => $loyaltyPreview['loyalty_discount'],
            'total' => $loyaltyPreview['total'],
            'points_to_earn' => $loyaltyPreview['points_to_earn'],
            'points_balance' => $loyaltyPreview['points_balance'],
        ]);
    }

    private function previewWallet(Client $client, float $subtotal, ?float $walletAmountToUse, array $base): array
    {
        $walletPreview = $this->wallets->previewPayment($client, $subtotal, $walletAmountToUse);

        return $this->withEarnOnly($client, $walletPreview['total'], array_merge($base, [
            'discount_type' => 'wallet',
            'wallet_amount_to_use' => $walletPreview['wallet_amount_to_use'],
            'discount_amount' => $walletPreview['discount_amount'],
            'loyalty_discount' => $walletPreview['discount_amount'],
            'total' => $walletPreview['total'],
            'wallet_balance' => $walletPreview['wallet_balance'],
        ]));
    }

    private function withEarnOnly(Client $client, float $paidAmount, array $base): array
    {
        $earnPreview = $this->loyalty->preview($client, $paidAmount, 0);

        return array_merge($base, [
            'points_to_earn' => $earnPreview['points_to_earn'],
            'points_balance' => $earnPreview['points_balance'],
        ]);
    }

    private function isVoucherEligible(Voucher $voucher, float $subtotal, ?string $serviceType): bool
    {
        if (! $voucher->is_active) {
            return false;
        }

        if ($voucher->valid_from && $voucher->valid_from->isFuture()) {
            return false;
        }

        if ($voucher->valid_to && $voucher->valid_to->endOfDay()->isPast()) {
            return false;
        }

        if ($voucher->min_amount && $subtotal < (float) $voucher->min_amount) {
            return false;
        }

        if ($voucher->service_target && $serviceType && $voucher->service_target !== $serviceType) {
            return false;
        }

        return true;
    }

    private function isCouponEligible(Client $client, Coupon $coupon, float $subtotal): bool
    {
        try {
            $this->coupons->validateForPayment($client, $coupon, $subtotal);

            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    private function voucherPayload(Voucher $voucher, float $subtotal): array
    {
        $discount = $this->vouchers->calculateDiscount($voucher, $subtotal);
        $type = $voucher->discount_type?->value ?? $voucher->discount_type;

        return [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'title' => $voucher->title,
            'discount_amount' => $discount,
            'discount_label' => $type === 'percentage'
                ? rtrim(rtrim(number_format((float) $voucher->discount_value, 2), '0'), '.') . '% OFF'
                : '$' . number_format((float) $voucher->discount_value, 2) . ' OFF',
        ];
    }

    private function couponPayload(Coupon $coupon, float $subtotal): array
    {
        return [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'discount_type' => $coupon->discount_type,
            'value' => $coupon->value,
            'discount_amount' => $this->coupons->calculateDiscount($coupon, $subtotal),
        ];
    }
}

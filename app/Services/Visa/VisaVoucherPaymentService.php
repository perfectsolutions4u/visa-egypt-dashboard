<?php

namespace App\Services\Visa;

use App\Enums\CouponType;
use App\Models\Client;
use App\Models\Visa\ClientVoucher;
use App\Models\Visa\VisaPayment;
use App\Models\Visa\Voucher;
use Illuminate\Validation\ValidationException;

class VisaVoucherPaymentService
{
    public function findInWallet(Client $client, int $voucherId): ?Voucher
    {
        return $client->vouchers()
            ->where('visa_vouchers.id', $voucherId)
            ->first();
    }

    public function validateForPayment(
        Client $client,
        Voucher $voucher,
        float $subtotal,
        ?string $serviceType = null
    ): void {
        if (! $voucher->is_active) {
            throw ValidationException::withMessages([
                'voucher_id' => ['This voucher is not active.'],
            ]);
        }

        if ($voucher->valid_from && $voucher->valid_from->isFuture()) {
            throw ValidationException::withMessages([
                'voucher_id' => ['This voucher is not valid yet.'],
            ]);
        }

        if ($voucher->valid_to && $voucher->valid_to->endOfDay()->isPast()) {
            throw ValidationException::withMessages([
                'voucher_id' => ['This voucher has expired.'],
            ]);
        }

        $inWallet = $client->vouchers()
            ->where('visa_vouchers.id', $voucher->id)
            ->exists();

        if (! $inWallet) {
            throw ValidationException::withMessages([
                'voucher_id' => ['This voucher is not in your wallet.'],
            ]);
        }

        $alreadyUsed = VisaPayment::query()
            ->where('client_id', $client->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyUsed) {
            throw ValidationException::withMessages([
                'voucher_id' => ['This voucher has already been used on a payment.'],
            ]);
        }

        if ($voucher->min_amount && $subtotal < (float) $voucher->min_amount) {
            throw ValidationException::withMessages([
                'voucher_id' => ["Minimum order amount is \${$voucher->min_amount}."],
            ]);
        }

        if ($voucher->service_target && $serviceType && $voucher->service_target !== $serviceType) {
            throw ValidationException::withMessages([
                'voucher_id' => ['This voucher is not valid for this service.'],
            ]);
        }
    }

    public function calculateDiscount(Voucher $voucher, float $subtotal): float
    {
        $type = $voucher->discount_type?->value ?? $voucher->discount_type;

        $discount = match ($type) {
            CouponType::PERCENTAGE->value => $subtotal * ((float) $voucher->discount_value / 100),
            default => (float) $voucher->discount_value,
        };

        return round(max(0, min($subtotal, $discount)), 2);
    }

    public function markUsed(Client $client, Voucher $voucher): void
    {
        $voucher->increment('used_count');

        ClientVoucher::query()
            ->where('client_id', $client->id)
            ->where('voucher_id', $voucher->id)
            ->update(['used_at' => now()]);
    }
}

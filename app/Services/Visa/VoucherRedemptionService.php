<?php

namespace App\Services\Visa;

use App\Models\Client;
use App\Models\Visa\ClientVoucher;
use App\Models\Visa\Voucher;
use Illuminate\Validation\ValidationException;

class VoucherRedemptionService
{
    public function redeem(Client $client, string $code): Voucher
    {
        $voucher = Voucher::query()
            ->where('code', strtoupper(trim($code)))
            ->first();

        if (! $voucher) {
            throw ValidationException::withMessages([
                'code' => ['Invalid voucher code.'],
            ]);
        }

        $this->assertRedeemable($client, $voucher);

        ClientVoucher::firstOrCreate(
            [
                'client_id' => $client->id,
                'voucher_id' => $voucher->id,
            ],
            ['redeemed_at' => now()]
        );

        return $client->vouchers()
            ->where('visa_vouchers.id', $voucher->id)
            ->first();
    }

    public function assertRedeemable(Client $client, Voucher $voucher): void
    {
        if (! $voucher->is_active) {
            throw ValidationException::withMessages([
                'code' => ['This voucher is not active.'],
            ]);
        }

        if ($voucher->valid_from && $voucher->valid_from->isFuture()) {
            throw ValidationException::withMessages([
                'code' => ['This voucher is not valid yet.'],
            ]);
        }

        if ($voucher->valid_to && $voucher->valid_to->endOfDay()->isPast()) {
            throw ValidationException::withMessages([
                'code' => ['This voucher has expired.'],
            ]);
        }

        if ($voucher->client_id && (int) $voucher->client_id !== (int) $client->id) {
            throw ValidationException::withMessages([
                'code' => ['This voucher is not assigned to your account.'],
            ]);
        }

        if ($voucher->max_uses !== null && $voucher->used_count >= $voucher->max_uses) {
            throw ValidationException::withMessages([
                'code' => ['This voucher has reached its usage limit.'],
            ]);
        }

        $alreadyRedeemed = ClientVoucher::query()
            ->where('client_id', $client->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyRedeemed) {
            throw ValidationException::withMessages([
                'code' => ['You have already added this voucher.'],
            ]);
        }
    }
}

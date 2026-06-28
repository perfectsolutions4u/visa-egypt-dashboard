<?php

namespace App\Services\Visa;

use App\Models\Client;
use App\Models\Visa\Membership;
use App\Models\Visa\PointsTransaction;
use App\Models\Visa\VisaPayment;
use Illuminate\Support\Facades\DB;

class PointsWalletTransferService
{
    public function __construct(
        private readonly LoyaltySettingsService $settings,
        private readonly WalletService $wallets
    ) {}

    public function pointsToUsd(int $points): float
    {
        $rate = (int) $this->settings->get()['redeem_points_per_usd'];

        if ($rate <= 0 || $points <= 0) {
            return 0.0;
        }

        return round($points / $rate, 2);
    }

    public function creditAndTransferToWallet(
        Client $client,
        Membership $membership,
        int $points,
        string $sourceType,
        string $description,
        ?VisaPayment $payment = null
    ): array {
        if ($points <= 0) {
            return ['points' => 0, 'wallet_credited' => 0.0];
        }

        $usd = $this->pointsToUsd($points);

        if ($usd <= 0) {
            $membership->increment('points_balance', $points);

            return ['points' => $points, 'wallet_credited' => 0.0];
        }

        PointsTransaction::create([
            'client_id' => $client->id,
            'membership_id' => $membership->id,
            'visa_payment_id' => $payment?->id,
            'type' => $sourceType,
            'points' => $points,
            'amount_usd' => $usd,
            'description' => $description,
        ]);

        PointsTransaction::create([
            'client_id' => $client->id,
            'membership_id' => $membership->id,
            'visa_payment_id' => $payment?->id,
            'type' => 'wallet_transfer',
            'points' => -$points,
            'amount_usd' => $usd,
            'description' => 'Auto-transferred to wallet',
        ]);

        $this->wallets->creditFromPoints($client, $usd, $description);

        return [
            'points' => $points,
            'wallet_credited' => $usd,
        ];
    }

    public function transferPendingBalance(Client $client): ?array
    {
        $membership = $client->activeMembership;

        if (! $membership || (int) $membership->points_balance <= 0) {
            return null;
        }

        $points = (int) $membership->points_balance;
        $usd = $this->pointsToUsd($points);

        if ($usd <= 0) {
            return null;
        }

        return DB::transaction(function () use ($client, $membership, $points, $usd) {
            $membership->update(['points_balance' => 0]);

            PointsTransaction::create([
                'client_id' => $client->id,
                'membership_id' => $membership->id,
                'type' => 'wallet_transfer',
                'points' => -$points,
                'amount_usd' => $usd,
                'description' => 'Pending points transferred to wallet',
            ]);

            $this->wallets->creditFromPoints(
                $client,
                $usd,
                'Points balance transferred to wallet'
            );

            return [
                'points_transferred' => $points,
                'wallet_credited' => $usd,
            ];
        });
    }
}

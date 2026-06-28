<?php

namespace App\Services\Visa;

use App\Models\Client;
use App\Models\Visa\Membership;
use App\Models\Visa\PointsTransaction;
use App\Models\Visa\VisaPayment;
use Illuminate\Validation\ValidationException;

class LoyaltyService
{
    public function __construct(
        private readonly LoyaltySettingsService $settings,
        private readonly PointsWalletTransferService $pointsWallet,
        private readonly WalletService $wallets
    ) {}

    public function getSettings(): array
    {
        return $this->settings->get();
    }

    public function activeMembership(Client $client): ?Membership
    {
        return $client->activeMembership;
    }

    public function getBalance(Client $client): int
    {
        return (int) ($this->activeMembership($client)?->points_balance ?? 0);
    }

    public function syncPointsToWallet(Client $client): ?array
    {
        return $this->pointsWallet->transferPendingBalance($client);
    }

    public function preview(Client $client, float $subtotal, int $pointsToUse = 0): array
    {
        $config = $this->settings->get();
        $balance = $this->getBalance($client);
        $membership = $this->activeMembership($client);

        $base = [
            'enabled' => (bool) $config['enabled'],
            'points_balance' => $balance,
            'has_membership' => $membership !== null,
            'subtotal' => round($subtotal, 2),
            'points_to_use' => 0,
            'loyalty_discount' => 0.0,
            'total' => round($subtotal, 2),
            'points_to_earn' => 0,
            'earn_points_per_usd' => (int) $config['earn_points_per_usd'],
            'redeem_points_per_usd' => (int) $config['redeem_points_per_usd'],
            'min_points_redeem' => (int) $config['min_points_redeem'],
            'max_redeem_percent' => (float) $config['max_redeem_percent'],
        ];

        if (! $config['enabled'] || ! $membership || $subtotal <= 0) {
            return $base;
        }

        $pointsToUse = max(0, min($pointsToUse, $balance));

        if ($pointsToUse > 0 && $pointsToUse < (int) $config['min_points_redeem']) {
            $pointsToUse = 0;
        }

        $discount = 0.0;
        if ($pointsToUse > 0 && (int) $config['redeem_points_per_usd'] > 0) {
            $discount = $pointsToUse / (int) $config['redeem_points_per_usd'];
            $maxDiscount = $subtotal * ((float) $config['max_redeem_percent'] / 100);
            $discount = min($discount, $maxDiscount, $subtotal);
            $pointsToUse = (int) round($discount * (int) $config['redeem_points_per_usd']);
        }

        $total = max(0.01, round($subtotal - $discount, 2));
        $pointsToEarn = (int) floor($total * (int) $config['earn_points_per_usd']);

        return array_merge($base, [
            'points_to_use' => $pointsToUse,
            'loyalty_discount' => round($discount, 2),
            'total' => $total,
            'points_to_earn' => $pointsToEarn,
        ]);
    }

    public function applyToPayment(Client $client, float $subtotal, int $pointsToUse): array
    {
        $preview = $this->preview($client, $subtotal, $pointsToUse);

        if ($pointsToUse > 0 && $preview['points_to_use'] <= 0) {
            throw ValidationException::withMessages([
                'points_to_use' => ['Not enough points or below minimum redeem amount.'],
            ]);
        }

        return $preview;
    }

    public function redeemForPayment(
        Client $client,
        VisaPayment $payment,
        int $pointsUsed,
        float $discountAmount
    ): void {
        if ($pointsUsed <= 0) {
            return;
        }

        $membership = $this->activeMembership($client);
        if (! $membership) {
            throw ValidationException::withMessages([
                'points_to_use' => ['Active membership required to redeem points.'],
            ]);
        }

        if ($this->wallets->getBalance($client) < $discountAmount) {
            throw ValidationException::withMessages([
                'points_to_use' => ['Insufficient wallet balance for this redemption.'],
            ]);
        }

        $this->wallets->debitForPayment($client, $payment, $discountAmount);

        PointsTransaction::create([
            'client_id' => $client->id,
            'membership_id' => $membership->id,
            'visa_payment_id' => $payment->id,
            'type' => 'redeem',
            'points' => -$pointsUsed,
            'amount_usd' => $discountAmount,
            'description' => 'Points redeemed via wallet for payment '.$payment->gateway_reference,
        ]);
    }

    public function earnForPayment(Client $client, VisaPayment $payment, int $pointsEarned): void
    {
        if ($pointsEarned <= 0) {
            return;
        }

        $membership = $this->activeMembership($client);
        if (! $membership) {
            return;
        }

        $this->pointsWallet->creditAndTransferToWallet(
            $client,
            $membership,
            $pointsEarned,
            'earn',
            'Points earned from payment '.$payment->gateway_reference,
            $payment
        );
    }

    public function completePaymentRewards(Client $client, VisaPayment $payment): void
    {
        if ($payment->points_earned > 0) {
            $alreadyEarned = PointsTransaction::query()
                ->where('visa_payment_id', $payment->id)
                ->where('type', 'earn')
                ->exists();

            if (! $alreadyEarned) {
                $this->earnForPayment($client, $payment, (int) $payment->points_earned);
            }
        }
    }
}

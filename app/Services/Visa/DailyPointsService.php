<?php

namespace App\Services\Visa;

use App\Models\Client;
use App\Models\Visa\Membership;
use App\Models\Visa\MembershipTier;
use App\Models\Visa\PointsTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DailyPointsService
{
    public function __construct(
        private readonly LoyaltySettingsService $settings,
        private readonly PointsWalletTransferService $pointsWallet
    ) {}

    public function planForMembership(Membership $membership): ?MembershipTier
    {
        return MembershipTier::query()
            ->where('slug', $membership->plan_type)
            ->where('is_active', true)
            ->first();
    }

    public function status(Client $client, ?Membership $membership = null): array
    {
        $membership ??= $client->activeMembership;

        if (! $membership || $membership->status !== 'active') {
            return $this->emptyStatus();
        }

        $plan = $this->planForMembership($membership);
        $pointsPerDay = (int) ($plan?->daily_points ?? 0);

        if ($pointsPerDay <= 0) {
            return array_merge($this->emptyStatus(), [
                'enabled' => false,
                'points_per_day' => 0,
            ]);
        }

        $claimedToday = $this->hasClaimedToday($membership);

        return [
            'enabled' => true,
            'points_per_day' => $pointsPerDay,
            'can_claim' => ! $claimedToday,
            'claimed_today' => $claimedToday,
            'plan_name' => $plan?->name,
        ];
    }

    public function claim(Client $client): array
    {
        $loyaltySettings = $this->settings->get();

        if (! $loyaltySettings['enabled']) {
            throw ValidationException::withMessages([
                'daily_points' => ['Loyalty program is not enabled.'],
            ]);
        }

        $membership = $client->activeMembership;

        if (! $membership || $membership->status !== 'active') {
            throw ValidationException::withMessages([
                'daily_points' => ['Active membership required to claim daily points.'],
            ]);
        }

        $plan = $this->planForMembership($membership);
        $points = (int) ($plan?->daily_points ?? 0);

        if ($points <= 0) {
            throw ValidationException::withMessages([
                'daily_points' => ['Your plan does not include daily points.'],
            ]);
        }

        if ($this->hasClaimedToday($membership)) {
            throw ValidationException::withMessages([
                'daily_points' => ['You have already claimed your daily points today.'],
            ]);
        }

        return DB::transaction(function () use ($client, $membership, $points, $plan) {
            $transfer = $this->pointsWallet->creditAndTransferToWallet(
                $client,
                $membership,
                $points,
                'daily_claim',
                'Daily points — '.($plan?->name ?? $membership->plan_type)
            );

            $membership->refresh();

            return [
                'points_claimed' => $points,
                'wallet_credited' => $transfer['wallet_credited'],
                'points_balance' => (int) $membership->points_balance,
                'claimed_today' => true,
                'can_claim' => false,
                'points_per_day' => $points,
                'plan_name' => $plan?->name,
            ];
        });
    }

    public function hasClaimedToday(Membership $membership): bool
    {
        return PointsTransaction::query()
            ->where('membership_id', $membership->id)
            ->where('type', 'daily_claim')
            ->whereDate('created_at', today())
            ->exists();
    }

    private function emptyStatus(): array
    {
        return [
            'enabled' => false,
            'points_per_day' => 0,
            'can_claim' => false,
            'claimed_today' => false,
            'plan_name' => null,
        ];
    }
}

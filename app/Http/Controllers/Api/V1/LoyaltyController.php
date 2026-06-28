<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PreviewLoyaltyRequest;
use App\Http\Resources\Api\V1\PointsTransactionResource;
use App\Models\Visa\PointsTransaction;
use App\Services\Visa\DailyPointsService;
use App\Services\Visa\LoyaltyService;
use App\Services\Visa\WalletService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    use HasApiResponse;

    public function show(
        Request $request,
        LoyaltyService $loyalty,
        DailyPointsService $dailyPoints,
        WalletService $wallets
    ) {
        $client = $request->user();
        $loyalty->syncPointsToWallet($client);
        $settings = $loyalty->getSettings();

        return $this->send([
            'enabled' => (bool) $settings['enabled'],
            'points_balance' => $loyalty->getBalance($client),
            'wallet_balance' => $wallets->getBalance($client),
            'auto_transfer_to_wallet' => true,
            'has_membership' => $loyalty->activeMembership($client) !== null,
            'earn_points_per_usd' => (int) $settings['earn_points_per_usd'],
            'redeem_points_per_usd' => (int) $settings['redeem_points_per_usd'],
            'min_points_redeem' => (int) $settings['min_points_redeem'],
            'max_redeem_percent' => (float) $settings['max_redeem_percent'],
            'redeem_value_per_point' => (int) $settings['redeem_points_per_usd'] > 0
                ? round(1 / (int) $settings['redeem_points_per_usd'], 4)
                : 0,
            'daily_points' => $dailyPoints->status($client),
        ]);
    }

    public function preview(PreviewLoyaltyRequest $request, LoyaltyService $loyalty)
    {
        return $this->send(
            $loyalty->preview(
                $request->user(),
                (float) $request->validated('subtotal'),
                (int) ($request->validated('points_to_use') ?? 0)
            )
        );
    }

    public function transactions(Request $request)
    {
        $transactions = PointsTransaction::query()
            ->where('client_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->send(
            PointsTransactionResource::collection($transactions)->response()->getData(true)
        );
    }

    public function claimDaily(Request $request, DailyPointsService $dailyPoints)
    {
        try {
            $result = $dailyPoints->claim($request->user());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Could not claim daily points.';

            return $this->send(null, $message, 422);
        }

        return $this->send($result, 'Daily points claimed successfully.');
    }
}

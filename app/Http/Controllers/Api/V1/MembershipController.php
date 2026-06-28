<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MembershipCheckoutRequest;
use App\Http\Resources\Api\V1\MembershipResource;
use App\Http\Resources\Api\V1\VisaPaymentResource;
use App\Enums\Visa\VisaPaymentMethod;
use App\Services\Visa\DailyPointsService;
use App\Services\Visa\LoyaltyService;
use App\Services\Visa\MembershipCheckoutService;
use App\Services\Visa\WalletService;
use App\Traits\Response\HasApiResponse;

class MembershipController extends Controller
{
    use HasApiResponse;

    public function show(
        \Illuminate\Http\Request $request,
        DailyPointsService $dailyPoints,
        LoyaltyService $loyalty,
        WalletService $wallets
    ) {
        $client = $request->user();
        $loyalty->syncPointsToWallet($client);
        $membership = $client->activeMembership;

        if (! $membership) {
            return $this->send(null);
        }

        $data = (new MembershipResource($membership))->toArray($request);
        $data['daily_points'] = $dailyPoints->status($client, $membership);
        $data['wallet_balance'] = $wallets->getBalance($client);
        $data['auto_transfer_to_wallet'] = true;

        return $this->send($data);
    }

    public function checkout(MembershipCheckoutRequest $request, MembershipCheckoutService $checkout)
    {
        $method = VisaPaymentMethod::from($request->get('payment_method'));

        $result = $checkout->checkout($request->user(), $request->get('plan_type'), $method);

        return $this->send([
            'membership' => new MembershipResource($result['membership']),
            'payment' => new VisaPaymentResource($result['payment']),
            'amount' => $result['amount'],
        ], 'Membership checkout initiated.', 201);
    }
}

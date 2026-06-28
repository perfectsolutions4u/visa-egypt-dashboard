<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RedeemVoucherRequest;
use App\Http\Resources\Api\V1\VoucherResource;
use App\Services\Visa\VoucherRedemptionService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    use HasApiResponse;

    public function index(Request $request)
    {
        $vouchers = $request->user()
            ->vouchers()
            ->orderByDesc('client_vouchers.redeemed_at')
            ->get();

        return $this->send(VoucherResource::collection($vouchers));
    }

    public function redeem(RedeemVoucherRequest $request, VoucherRedemptionService $redemption)
    {
        try {
            $voucher = $redemption->redeem($request->user(), $request->validated('code'));
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Invalid voucher code.';

            return $this->send(null, $message, 422);
        }

        return $this->send(
            new VoucherResource($voucher),
            'Voucher added to your wallet.',
            201
        );
    }
}

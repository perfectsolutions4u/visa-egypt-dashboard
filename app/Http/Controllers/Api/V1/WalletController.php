<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\WalletResource;
use App\Http\Resources\Api\V1\WalletTransactionResource;
use App\Services\Visa\WalletService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use HasApiResponse;

    public function show(Request $request, WalletService $wallets)
    {
        return $this->send(new WalletResource(
            $wallets->getOrCreateWallet($request->user())
        ));
    }

    public function transactions(Request $request, WalletService $wallets)
    {
        $wallet = $wallets->getOrCreateWallet($request->user());
        $transactions = $wallet->transactions()->paginate($request->integer('per_page', 30));

        return $this->send(WalletTransactionResource::collection($transactions)->response()->getData(true));
    }
}

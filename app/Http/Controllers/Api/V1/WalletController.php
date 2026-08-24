<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Visa\VisaPaymentMethod;
use App\Enums\Visa\VisaPaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WalletTopUpRequest;
use App\Http\Requests\Api\V1\WalletTransferRequest;
use App\Http\Resources\Api\V1\VisaPaymentResource;
use App\Http\Resources\Api\V1\WalletResource;
use App\Http\Resources\Api\V1\WalletTransactionResource;
use App\Models\Client;
use App\Models\Visa\VisaPayment;
use App\Services\Visa\VisaPaymentGatewayService;
use App\Services\Visa\WalletService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    public function topUp(
        WalletTopUpRequest $request,
        WalletService $wallets,
        VisaPaymentGatewayService $gateway
    ) {
        $client = $request->user();
        $amount = (float) $request->validated('amount');
        $method = VisaPaymentMethod::from($request->validated('method'));

        $payment = DB::transaction(function () use ($client, $amount, $method, $gateway, $wallets) {
            $payment = VisaPayment::create([
                'client_id' => $client->id,
                'purpose' => 'wallet_topup',
                'subtotal' => $amount,
                'amount' => $amount,
                'currency' => config('visa_payment.currency', 'USD'),
                'method' => $method,
                'status' => VisaPaymentStatus::PENDING,
                'gateway_reference' => 'TOPUP-'.Str::upper(Str::random(10)),
            ]);

            $initiation = $gateway->initiate($payment);
            if ($initiation['auto_completed']) {
                $payment = $gateway->markCompleted($payment);
                $wallets->creditTopUp($client, $amount, $payment);
            } else {
                $payment->update(['status' => $initiation['status']]);
                $payment->setAttribute('payment_url', $initiation['payment_url']);
            }

            return $payment;
        });

        $resource = (new VisaPaymentResource($payment))->toArray($request);
        if ($payment->getAttribute('payment_url')) {
            $resource['payment_url'] = $payment->getAttribute('payment_url');
        }

        return $this->send($resource, 'Wallet top-up initiated.', 201);
    }

    public function transfer(WalletTransferRequest $request, WalletService $wallets)
    {
        $from = $request->user();
        $to = Client::whereEmail($request->validated('email'))->firstOrFail();

        try {
            $result = $wallets->transfer(
                $from,
                $to,
                (float) $request->validated('amount'),
                $request->validated('note')
            );
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Transfer failed.';

            return $this->send(null, $message, 422);
        }

        return $this->send([
            'from_balance' => $result['from_balance'],
            'to_balance' => $result['to_balance'],
            'wallet' => new WalletResource($wallets->getOrCreateWallet($from)),
        ], 'Transfer completed.');
    }
}

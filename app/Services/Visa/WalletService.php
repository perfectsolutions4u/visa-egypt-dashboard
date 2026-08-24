<?php

namespace App\Services\Visa;

use App\Enums\Visa\WalletTransactionType;
use App\Models\Client;
use App\Models\Visa\VisaPayment;
use App\Models\Visa\Wallet;
use App\Models\Visa\WalletTransaction;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function getOrCreateWallet(Client $client): Wallet
    {
        return $client->wallet()->firstOrCreate(
            ['client_id' => $client->id],
            ['balance' => 0, 'bonus_credit' => 0, 'currency' => 'USD']
        );
    }

    public function getBalance(Client $client): float
    {
        return (float) $this->getOrCreateWallet($client)->balance;
    }

    public function previewPayment(Client $client, float $subtotal, ?float $amountToUse = null): array
    {
        $balance = $this->getBalance($client);
        $maxUsable = min($balance, $subtotal);
        $walletAmount = $amountToUse !== null
            ? min(max(0, $amountToUse), $maxUsable)
            : $maxUsable;

        if ($walletAmount <= 0) {
            throw ValidationException::withMessages([
                'wallet_amount_to_use' => ['Insufficient wallet balance for this payment.'],
            ]);
        }

        $total = max(0, round($subtotal - $walletAmount, 2));

        return [
            'wallet_balance' => round($balance, 2),
            'wallet_amount_to_use' => round($walletAmount, 2),
            'discount_amount' => round($walletAmount, 2),
            'total' => $total,
        ];
    }

    public function debitForPayment(Client $client, VisaPayment $payment, float $amount): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($client);

        if ((float) $wallet->balance < $amount) {
            throw ValidationException::withMessages([
                'wallet_amount_to_use' => ['Insufficient wallet balance.'],
            ]);
        }

        $wallet->decrement('balance', $amount);

        return $wallet->transactions()->create([
            'type' => WalletTransactionType::DEBIT,
            'amount' => $amount,
            'description' => 'Payment #'.$payment->id,
            'reference' => 'payment:'.$payment->id,
        ]);
    }

    public function creditFromPoints(Client $client, float $amount, string $description): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($client);
        $wallet->increment('balance', $amount);

        return $wallet->transactions()->create([
            'type' => WalletTransactionType::CREDIT,
            'amount' => $amount,
            'description' => $description,
            'reference' => 'points_transfer',
        ]);
    }

    public function adjustBalance(Client $client, float $amount, string $description, ?int $adminId = null): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($client);
        $wallet->increment('balance', $amount);

        return $wallet->transactions()->create([
            'type' => $amount >= 0 ? WalletTransactionType::CREDIT : WalletTransactionType::DEBIT,
            'amount' => abs($amount),
            'description' => $description,
            'reference' => 'admin_adjustment',
        ]);
    }

    public function creditTopUp(Client $client, float $amount, VisaPayment $payment): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($client);
        $wallet->increment('balance', $amount);

        return $wallet->transactions()->create([
            'type' => WalletTransactionType::CREDIT,
            'amount' => $amount,
            'description' => 'Wallet top-up',
            'reference' => 'topup:'.$payment->id,
        ]);
    }

    public function transfer(Client $from, Client $to, float $amount, ?string $note = null): array
    {
        if ($from->id === $to->id) {
            throw ValidationException::withMessages([
                'email' => ['You cannot transfer to yourself.'],
            ]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => ['Transfer amount must be greater than zero.'],
            ]);
        }

        $fromWallet = $this->getOrCreateWallet($from);
        if ((float) $fromWallet->balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => ['Insufficient wallet balance.'],
            ]);
        }

        $toWallet = $this->getOrCreateWallet($to);
        $description = $note ?: 'Wallet transfer';

        $fromWallet->decrement('balance', $amount);
        $debit = $fromWallet->transactions()->create([
            'type' => WalletTransactionType::DEBIT,
            'amount' => $amount,
            'description' => $description.' → '.$to->email,
            'reference' => 'transfer_out:'.$to->id,
        ]);

        $toWallet->increment('balance', $amount);
        $credit = $toWallet->transactions()->create([
            'type' => WalletTransactionType::CREDIT,
            'amount' => $amount,
            'description' => $description.' ← '.$from->email,
            'reference' => 'transfer_in:'.$from->id,
        ]);

        return [
            'from_balance' => round((float) $fromWallet->fresh()->balance, 2),
            'to_balance' => round((float) $toWallet->fresh()->balance, 2),
            'debit' => $debit,
            'credit' => $credit,
        ];
    }
}
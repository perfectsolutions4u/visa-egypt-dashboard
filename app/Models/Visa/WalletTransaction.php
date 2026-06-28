<?php

namespace App\Models\Visa;

use App\Enums\Visa\WalletTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = ['wallet_id', 'type', 'amount', 'reference', 'description'];

    protected $casts = [
        'amount' => 'float',
        'type' => WalletTransactionType::class,
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}

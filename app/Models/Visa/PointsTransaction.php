<?php

namespace App\Models\Visa;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsTransaction extends Model
{
    protected $fillable = [
        'client_id',
        'membership_id',
        'visa_payment_id',
        'type',
        'points',
        'amount_usd',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
        'amount_usd' => 'float',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(VisaPayment::class, 'visa_payment_id');
    }
}

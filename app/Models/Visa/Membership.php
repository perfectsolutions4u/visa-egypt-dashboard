<?php

namespace App\Models\Visa;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $fillable = [
        'client_id', 'plan_type', 'discount_percent', 'points_balance',
        'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'discount_percent' => 'float',
        'points_balance' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VisaPayment::class);
    }

    public function pointsTransactions(): HasMany
    {
        return $this->hasMany(PointsTransaction::class);
    }
}

<?php

namespace App\Models\Visa;

use App\Enums\CouponType;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    protected $table = 'visa_vouchers';

    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'min_amount',
        'service_target',
        'client_id',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'min_amount' => 'float',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
        'discount_type' => CouponType::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_vouchers')
            ->withPivot('redeemed_at')
            ->withTimestamps();
    }

    public function membershipPlans(): BelongsToMany
    {
        return $this->belongsToMany(MembershipTier::class, 'membership_plan_voucher', 'voucher_id', 'membership_plan_id')
            ->withTimestamps();
    }
}

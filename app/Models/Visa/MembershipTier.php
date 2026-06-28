<?php

namespace App\Models\Visa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class MembershipTier extends Model
{
    protected $table = 'membership_plans';

    protected $fillable = [
        'slug',
        'name',
        'tagline',
        'description',
        'features',
        'special_offer_text',
        'special_offer_included',
        'theme_color',
        'is_featured',
        'discount_percent',
        'price_usd',
        'daily_points',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'special_offer_included' => 'boolean',
        'is_featured' => 'boolean',
        'discount_percent' => 'float',
        'price_usd' => 'float',
        'daily_points' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function activeOrdered(): Collection
    {
        return static::query()
            ->with(['vouchers', 'coupons'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public static function activeSlugs(): array
    {
        return static::activeOrdered()->pluck('slug')->all();
    }

    public static function optionsForSelect(): array
    {
        return static::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('name', 'slug')
            ->all();
    }

    public static function discountMap(): array
    {
        return static::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('discount_percent', 'slug')
            ->all();
    }

    public static function findActiveBySlug(string $slug): ?self
    {
        return static::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function toApiArray(): array
    {
        return [
            'tier' => $this->slug,
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'features' => $this->features ?? [],
            'special_offer' => [
                'text' => $this->special_offer_text,
                'included' => (bool) $this->special_offer_included,
            ],
            'theme_color' => $this->theme_color,
            'is_featured' => (bool) $this->is_featured,
            'discount_percent' => $this->discount_percent,
            'price_usd' => $this->price_usd,
            'daily_points' => (int) $this->daily_points,
            'included_vouchers' => $this->vouchers
                ->where('is_active', true)
                ->map(fn (Voucher $voucher) => [
                    'code' => $voucher->code,
                    'title' => $voucher->title,
                    'discount_label' => $voucher->discount_type?->value === 'percentage'
                        ? rtrim(rtrim(number_format((float) $voucher->discount_value, 2), '0'), '.') . '% OFF'
                        : '$' . number_format((float) $voucher->discount_value, 2) . ' OFF',
                ])
                ->values()
                ->all(),
            'included_coupons' => $this->coupons
                ->where('active', true)
                ->map(fn (\App\Models\Coupon $coupon) => [
                    'code' => $coupon->code,
                    'title' => $coupon->title,
                    'discount_type' => $coupon->discount_type,
                    'value' => $coupon->value,
                ])
                ->values()
                ->all(),
        ];
    }

    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class, 'membership_plan_voucher', 'membership_plan_id', 'voucher_id')
            ->withTimestamps();
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Coupon::class, 'membership_plan_coupon', 'membership_plan_id', 'coupon_id')
            ->withTimestamps();
    }
}

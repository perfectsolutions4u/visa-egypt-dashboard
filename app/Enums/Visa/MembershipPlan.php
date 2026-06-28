<?php

namespace App\Enums\Visa;

use App\Models\Visa\MembershipTier;
use Illuminate\Support\Facades\Schema;

enum MembershipPlan: string
{
    case SILVER = 'silver';
    case GOLD = 'gold';
    case PLATINUM = 'platinum';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public static function allowedSlugs(): array
    {
        if (Schema::hasTable('membership_plans')) {
            $slugs = MembershipTier::activeSlugs();
            if ($slugs !== []) {
                return $slugs;
            }
        }

        return self::all();
    }

    public function discountPercent(): float
    {
        if (Schema::hasTable('membership_plans')) {
            $tier = MembershipTier::query()->where('slug', $this->value)->first();
            if ($tier) {
                return (float) $tier->discount_percent;
            }
        }

        return match ($this) {
            self::SILVER => 10,
            self::GOLD => 15,
            self::PLATINUM => 25,
        };
    }
}

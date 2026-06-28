<?php

namespace App\Enums\Visa;

enum VisaBookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case ASSIGNED = 'assigned';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return str($this->value)->headline()->toString();
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-warning',
            self::CONFIRMED => 'badge-info',
            self::ASSIGNED => 'badge-primary',
            self::IN_PROGRESS => 'badge-secondary',
            self::COMPLETED => 'badge-success',
            self::CANCELLED, self::REJECTED => 'badge-danger',
        };
    }
}

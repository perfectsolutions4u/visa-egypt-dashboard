<?php

namespace App\Models\Visa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AdditionalService extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'currency',
        'price_from',
        'icon',
        'accent_color',
        'features',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'price_from' => 'boolean',
        'features' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Icons the mobile app knows how to render, keyed by Material icon name.
     */
    public static function iconOptions(): array
    {
        return [
            'sim_card' => 'SIM Card',
            'wifi' => 'Internet / WiFi',
            'local_taxi' => 'Airport Transfer',
            'directions_car' => 'Private Car',
            'apartment' => 'Hotel Building',
            'hotel' => 'Hotel Room',
            'directions_boat' => 'Nile Cruise',
            'restaurant' => 'Dining',
            'tour' => 'Tour / Sightseeing',
            'flight' => 'Flight',
            'luggage' => 'Luggage',
            'medical_services' => 'Medical / Insurance',
            'support_agent' => 'Support',
            'local_offer' => 'Generic Service',
        ];
    }

    /**
     * Accent colours matching the mobile design palette.
     */
    public static function colorOptions(): array
    {
        return [
            '#F26522' => 'Orange',
            '#0E7C7B' => 'Teal',
            '#D4A017' => 'Gold',
            '#D32027' => 'Red',
            '#0F2847' => 'Navy',
            '#0B6B3A' => 'Green',
            '#1B62C4' => 'Blue',
            '#6D28D9' => 'Purple',
        ];
    }

    public static function activeOrdered(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency ?: 'USD',
            'price_from' => (bool) $this->price_from,
            'icon' => $this->icon ?: 'local_offer',
            'accent_color' => $this->accent_color ?: '#0F2847',
            'features' => $this->features ?? [],
            'sort_order' => (int) $this->sort_order,
        ];
    }
}

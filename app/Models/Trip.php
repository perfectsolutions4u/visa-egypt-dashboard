<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Trip extends Model
{
    use HasFactory, SoftDeletes, Enabled;

    protected $fillable = [
        'trip_type', // one_way, round_trip, special_discount
        'trip_name',
        'departure_city_id',
        'arrival_city_id',
        'travel_date',
        'return_date',
        'seat_price',
        'available_seats',
        'departure_time',
        'arrival_time',
        'additional_notes',
        'amenities',
        'enabled',
        'total_seats'
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'seat_price' => 'decimal:2',
        'available_seats' => 'integer',
        'total_seats' => 'integer',
        'amenities' => 'array',
        'enabled' => 'boolean',
        'departure_city_id' => 'integer',
        'arrival_city_id' => 'integer',
    ];

    protected $appends = [
        'trip_type_label',
        'formatted_departure_time',
        'formatted_arrival_time',
        'is_available',
        'booked_seats',
        'formatted_price',
        'formatted_total_price',
        'occupancy_rate',
        'occupancy_status',
        'departure_city_name',
        'arrival_city_name'
    ];

    // Trip type constants
    const TYPE_ONE_WAY = 'one_way';
    const TYPE_ROUND_TRIP = 'round_trip';
    const TYPE_SPECIAL_DISCOUNT = 'special_discount';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($trip) {
            $trip->generateTripName();
        });

        static::updating(function ($trip) {
            if ($trip->isDirty(['departure_city_id', 'arrival_city_id', 'trip_type'])) {
                $trip->generateTripName();
            }
        });
    }

    public function generateTripName()
    {
        if ($this->departureCity && $this->arrivalCity) {
            $tripName = $this->departureCity->name . ' to ' . $this->arrivalCity->name;
            
            if ($this->trip_type === 'round_trip') {
                $tripName .= ' (Round Trip)';
            } elseif ($this->trip_type === 'special_discount') {
                $tripName .= ' (Special Discount)';
            }
            
            $this->trip_name = $tripName;
        }
    }

    public static function getTripTypes(): array
    {
        return [
            self::TYPE_ONE_WAY => 'One Way',
            self::TYPE_ROUND_TRIP => 'Round Trip',
            self::TYPE_SPECIAL_DISCOUNT => 'Special Discount'
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TripBooking::class);
    }

    public function departureCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'departure_city_id');
    }

    public function arrivalCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'arrival_city_id');
    }

    public function getTripTypeLabelAttribute(): string
    {
        return self::getTripTypes()[$this->trip_type] ?? $this->trip_type;
    }

    public function getFormattedDepartureTimeAttribute(): string
    {
        return $this->departure_time ? $this->departure_time->format('H:i') : '';
    }

    public function getFormattedArrivalTimeAttribute(): string
    {
        return $this->arrival_time ? $this->arrival_time->format('H:i') : '';
    }

    public function getDepartureCityNameAttribute(): string
    {
        return $this->departureCity ? $this->departureCity->name : '';
    }

    public function getArrivalCityNameAttribute(): string
    {
        return $this->arrivalCity ? $this->arrivalCity->name : '';
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_seats > 0 && $this->enabled;
    }

    public function getBookedSeatsAttribute(): int
    {
        return $this->total_seats - $this->available_seats;
    }

    public function scopeAvailable($query)
    {
        return $query->where('enabled', true)->where('available_seats', '>', 0);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('trip_type', $type);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('travel_date', $date);
    }

    public function scopeByCities($query, $from, $to)
    {
        return $query->where('departure_city_id', $from)->where('arrival_city_id', $to);
    }

    public function scopeByDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('travel_date', [$startDate, $endDate]);
        }
        return $query->whereDate('travel_date', '>=', $startDate);
    }

    public function hasAvailableSeats(int $passengers): bool
    {
        return $this->available_seats >= $passengers;
    }

    public function calculateTotalPrice(int $passengers): float
    {
        return $this->seat_price * $passengers;
    }

    public function calculatePriceForPassengers(int $adults, int $children = 0): float
    {
        $adultPrice = $this->seat_price * $adults;
        $childPrice = $this->seat_price * 0.5 * $children; // 50% discount for children
        
        return $adultPrice + $childPrice;
    }

    public function calculatePriceBreakdown(int $adults, int $children = 0): array
    {
        $adultPrice = $this->seat_price * $adults;
        $childPrice = $this->seat_price * 0.5 * $children;
        $totalPrice = $adultPrice + $childPrice;

        return [
            'adult_price' => $adultPrice,
            'child_price' => $childPrice,
            'total_price' => $totalPrice,
            'adults' => $adults,
            'children' => $children,
            'seat_price' => $this->seat_price,
            'child_discount_rate' => 0.5
        ];
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->seat_price, 2) . ' EGP';
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->seat_price * $this->total_seats, 2) . ' EGP';
    }

    public function getOccupancyRateAttribute(): float
    {
        if ($this->total_seats === 0) {
            return 0;
        }
        return round((($this->total_seats - $this->available_seats) / $this->total_seats) * 100, 2);
    }

    public function getOccupancyStatusAttribute(): string
    {
        $rate = $this->occupancy_rate;
        
        if ($rate >= 90) {
            return 'Full';
        } elseif ($rate >= 75) {
            return 'Almost Full';
        } elseif ($rate >= 50) {
            return 'Half Full';
        } elseif ($rate >= 25) {
            return 'Available';
        } else {
            return 'Empty';
        }
    }
}

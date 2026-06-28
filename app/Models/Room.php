<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use App\Traits\Models\HasSeo;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * @property string $name
 * @property string $description
 */
class Room extends Model
{
    use HasSeo, Translatable, Enabled;

    public array $translatedAttributes = [
        'name',
        'description'
    ];

    protected $fillable = [
        'slug',
        'featured_image',
        'banner',
        'hotel_id',
        'gallery',
        'enabled',
        'bed_count',
        'room_type',
        'max_capacity',
        'bed_types',
        'night_price',
        'extra_bed_available',
        'extra_bed_price',
        'max_extra_beds',
        'extra_bed_description',
    ];

    protected $casts = [
        'gallery' => 'array',
        'enabled' => 'boolean',
        'extra_bed_price' => 'decimal:2',
        'max_extra_beds' => 'integer',
    ];

    protected $hidden = [
        'translations'
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(HotelRoomBooking::class);
    }

    /**
     * Calculate total price for room with extra beds
     */
    public function calculateTotalPrice(int $nights, int $extraBedsCount = 0): float
    {
        $basePrice = $this->night_price * $nights;
        $extraBedsPrice = 0;
        
        if ($extraBedsCount > 0 && $this->extra_bed_available) {
            $extraBedsPrice = $this->extra_bed_price * $extraBedsCount * $nights;
        }
        
        return $basePrice + $extraBedsPrice;
    }

    /**
     * Check if extra beds can be added
     */
    public function canAddExtraBeds(int $extraBedsCount): bool
    {
        return $this->extra_bed_available && $extraBedsCount <= $this->max_extra_beds;
    }

    /**
     * Get total capacity including extra beds
     */
    public function getTotalCapacity(): int
    {
        return $this->max_capacity + $this->max_extra_beds;
    }

    /**
     * Check if room is available for given dates
     */
    public function isAvailable($checkIn, $checkOut): bool
    {
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);

        // Check for overlapping bookings
        $overlappingBookings = $this->bookings()
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where(function ($q) use ($checkInDate, $checkOutDate) {
                    // Case 1: New booking starts during existing booking
                    $q->where('start_date', '<=', $checkInDate)
                      ->where('end_date', '>', $checkInDate);
                })->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                    // Case 2: New booking ends during existing booking
                    $q->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>=', $checkOutDate);
                })->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                    // Case 3: New booking contains existing booking
                    $q->where('start_date', '>=', $checkInDate)
                      ->where('end_date', '<=', $checkOutDate);
                });
            })
            ->whereIn('status', ['confirmed', 'pending']) // Only check confirmed and pending bookings
            ->exists();

        return !$overlappingBookings;
    }

    /**
     * Scope to filter available rooms for given dates
     */
    public function scopeAvailable($query, $checkIn, $checkOut)
    {
        return $query->whereDoesntHave('bookings', function ($bookingQuery) use ($checkIn, $checkOut) {
            $checkInDate = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);

            $bookingQuery->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where(function ($q) use ($checkInDate, $checkOutDate) {
                    // Case 1: New booking starts during existing booking
                    $q->where('start_date', '<=', $checkInDate)
                      ->where('end_date', '>', $checkInDate);
                })->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                    // Case 2: New booking ends during existing booking
                    $q->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>=', $checkOutDate);
                })->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                    // Case 3: New booking contains existing booking
                    $q->where('start_date', '>=', $checkInDate)
                      ->where('end_date', '<=', $checkOutDate);
                });
            })->whereIn('status', ['confirmed', 'pending']);
        });
    }
}

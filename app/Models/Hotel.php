<?php

namespace App\Models;

use App\Traits\Models\HasSeo;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property string $description
 */
class Hotel extends Model
{
    use Translatable, HasSeo;

    public array $translatedAttributes = [
        'name',
        'description',
        'city'
    ];

    protected $fillable = [
        'stars',
        'enabled',
        'featured_image',
        'banner',
        'gallery',
        'address',
        'map_iframe',
        'slug',
        'phone_contact',
        'whatsapp_contact'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'gallery' => 'array',
    ];

    protected $hidden = [
        'translations'
    ];

    public function rooms(): Builder|HasMany|Hotel
    {
        return $this->hasMany(Room::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenities');
    }

    /**
     * Scope to filter hotels by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->whereHas('translations', function ($q) use ($city) {
            $q->where('city', 'LIKE', '%' . $city . '%');
        });
    }

    /**
     * Load available rooms for given dates
     */
    public function loadAvailableRooms($checkIn, $checkOut)
    {
        $this->setRelation('availableRooms', 
            $this->rooms()
                ->where('enabled', true)
                ->available($checkIn, $checkOut)
                ->with('amenities')
                ->get()
        );

        return $this;
    }
}

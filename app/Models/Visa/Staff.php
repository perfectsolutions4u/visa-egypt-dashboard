<?php

namespace App\Models\Visa;

use App\Enums\Visa\StaffType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'user_id', 'type', 'full_name', 'phone', 'whatsapp', 'languages',
        'rating', 'reviews_count', 'photo', 'license_number', 'is_active',
    ];

    protected $casts = [
        'languages' => 'array',
        'rating' => 'float',
        'reviews_count' => 'integer',
        'is_active' => 'boolean',
        'type' => StaffType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(VisaBookingAssignment::class);
    }
}

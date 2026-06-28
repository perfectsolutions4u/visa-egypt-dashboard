<?php

namespace App\Models\Visa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    protected $fillable = [
        'visa_booking_id', 'status_key', 'status_label', 'event_at',
        'staff_id', 'is_current', 'notes',
    ];

    protected $casts = [
        'event_at' => 'datetime',
        'is_current' => 'boolean',
    ];

    public function visaBooking(): BelongsTo
    {
        return $this->belongsTo(VisaBooking::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}

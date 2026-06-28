<?php

namespace App\Models\Visa;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaBookingAssignment extends Model
{
    protected $fillable = [
        'visa_booking_id', 'staff_id', 'vehicle_id', 'assigned_at', 'assigned_by',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function visaBooking(): BelongsTo
    {
        return $this->belongsTo(VisaBooking::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}

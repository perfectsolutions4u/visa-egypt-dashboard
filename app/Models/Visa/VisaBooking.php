<?php

namespace App\Models\Visa;

use App\Enums\Visa\VisaBookingStatus;
use App\Enums\Visa\VisaServiceType;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VisaBooking extends Model
{
    use LogsActivity;

    protected $fillable = [
        'client_id',
        'booking_ref',
        'service_type',
        'status',
        'program_id',
        'service_package_id',
        'vehicle_id',
        'travel_date',
        'travelers_count',
        'nationality',
        'contact_email',
        'contact_whatsapp',
        'flight_number',
        'arrival_time',
        'meeting_point',
        'destination',
        'special_requests',
        'metadata',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'travelers_count' => 'integer',
        'total_amount' => 'float',
        'special_requests' => 'array',
        'metadata' => 'array',
        'service_type' => VisaServiceType::class,
        'status' => VisaBookingStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'notes'])->logOnlyDirty();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(VisaBookingAssignment::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VisaPayment::class);
    }

    public function currentTrackingEvent(): HasOne
    {
        return $this->hasOne(TrackingEvent::class)->where('is_current', true);
    }
}

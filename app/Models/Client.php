<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use SoftDeletes, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'image',
        'name',
        'email',
        'password',
        'phone',
        'whatsapp',
        'language',
        'nationality',
        'birthdate',
        'blocked',
    ];

    protected $casts = [
        'blocked' => 'boolean',
        'birthdate' => 'date',
    ];

    protected $hidden = ['password'];

    public function addresses(): HasMany
    {
        return $this->hasMany(ClientAddress::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function toursWishlist(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'client_tour_wishlist');
    }

    public function visaBookings(): HasMany
    {
        return $this->hasMany(\App\Models\Visa\VisaBooking::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(\App\Models\Visa\Membership::class);
    }

    public function activeMembership(): HasOne
    {
        return $this->hasOne(\App\Models\Visa\Membership::class)->where('status', 'active')->latest();
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(\App\Models\Visa\Wallet::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(\App\Models\Visa\AppNotification::class);
    }

    public function visaPayments(): HasMany
    {
        return $this->hasMany(\App\Models\Visa\VisaPayment::class);
    }

    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Visa\Voucher::class, 'client_vouchers')
            ->withPivot('redeemed_at')
            ->withTimestamps()
            ->orderByDesc('client_vouchers.redeemed_at');
    }
}

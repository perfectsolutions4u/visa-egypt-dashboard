<?php

namespace App\Models\Visa;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $fillable = [
        'client_id', 'title', 'body', 'type', 'target_screen', 'target_id', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}

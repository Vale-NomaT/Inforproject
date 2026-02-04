<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'driver_id',
        'child_id',
        'scheduled_date',
        'status',
        'distance_km',
        'pricing_tier',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'distance_km' => 'float',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(TripEvent::class, 'trip_id');
    }

    public function rating(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Rating::class, 'trip_id');
    }
}

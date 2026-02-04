<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'trip_id',
        'type',
        'created_at',
        'lat',
        'lng',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}

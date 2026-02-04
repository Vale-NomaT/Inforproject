<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverPerformanceScore extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'driver_id',
        'score',
        'avg_rating',
        'reliability',
        'punctuality',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'float',
        'avg_rating' => 'float',
        'reliability' => 'float',
        'punctuality' => 'float',
        'calculated_at' => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

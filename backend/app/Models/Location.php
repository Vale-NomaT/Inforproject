<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'country',
        'lat',
        'lng',
        'is_active',
    ];

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(DriverProfile::class, 'driver_locations', 'location_id', 'driver_id');
    }
}

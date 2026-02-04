<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverProfile extends Model
{
    protected $table = 'drivers';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'date_of_birth',
        'gov_id_number',
        'license_number',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'vehicle_color',
        'license_plate',
        'max_child_capacity',
        'vehicle_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'driver_locations', 'driver_id', 'location_id');
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'driver_schools', 'driver_id', 'school_id');
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'driver_id', 'id');
    }
}

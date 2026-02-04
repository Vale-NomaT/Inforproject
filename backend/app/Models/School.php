<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'lat',
        'lng',
        'is_active',
    ];

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(DriverProfile::class, 'driver_schools', 'school_id', 'driver_id');
    }
}

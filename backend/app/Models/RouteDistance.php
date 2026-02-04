<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteDistance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'school_id',
        'one_way_distance_km',
        'last_calculated',
    ];

    protected $casts = [
        'last_calculated' => 'datetime',
        'one_way_distance_km' => 'float',
    ];
}

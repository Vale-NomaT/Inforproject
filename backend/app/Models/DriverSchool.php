<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverSchool extends Model
{
    protected $table = 'driver_schools';

    public $timestamps = false;

    protected $fillable = [
        'driver_id',
        'school_id',
    ];
}

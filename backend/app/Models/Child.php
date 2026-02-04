<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    protected $table = 'children';

    protected $fillable = [
        'parent_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'school_id',
        'pickup_location_id',
        'relationship',
        'school_start_time',
        'school_end_time',
        'grade',
        'medical_notes',
    ];

    public function parentProfile(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'child_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'child_id');
    }
}

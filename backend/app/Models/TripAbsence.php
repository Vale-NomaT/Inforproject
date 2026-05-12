<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripAbsence extends Model
{
    protected $fillable = [
        'child_id',
        'parent_id',
        'start_date',
        'end_date',
        'run_type',
        'reason',
        'driver_notified_at',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'end_date'            => 'date',
        'driver_notified_at'  => 'datetime',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /** Human-readable run type label */
    public function runLabel(): string
    {
        return match($this->run_type) {
            'morning'   => 'Morning',
            'afternoon' => 'Afternoon',
            default     => 'Morning & Afternoon',
        };
    }

    /** True if this absence covers a given date + trip type */
    public function covers(\Carbon\Carbon $date, string $tripType): bool
    {
        if ($date->lt($this->start_date) || $date->gt($this->end_date)) {
            return false;
        }
        return $this->run_type === 'both' || $this->run_type === $tripType;
    }
}

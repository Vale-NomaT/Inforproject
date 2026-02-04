<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRequest extends Model
{
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_DECLINED = 'declined';

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'driver_id',
        'child_id',
        'status',
        'pricing_tier',
        'created_at',
        'responded_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }
}

<?php

namespace App\Services;

use App\Models\Child;
use App\Models\DriverProfile;
use Illuminate\Database\Eloquent\Collection;

class DriverMatchingService
{
    public function findDriversForChild(Child $child): Collection
    {
        return DriverProfile::query()
            ->join('users', 'drivers.id', '=', 'users.id')
            ->where('users.status', 'active')
            ->where('users.user_type', 'driver')
            ->whereHas('locations', function ($query) use ($child): void {
                $query->where('locations.id', $child->pickup_location_id);
            })
            ->whereHas('schools', function ($query) use ($child): void {
                $query->where('schools.id', $child->school_id);
            })
            ->select('drivers.*')
            ->distinct()
            ->get();
    }
}

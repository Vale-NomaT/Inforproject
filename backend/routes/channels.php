<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('trips.{tripId}', function ($user, $tripId) {
    $trip = \App\Models\Trip::with('child')->find($tripId);

    if (! $trip) {
        return false;
    }

    return (int) $user->id === (int) $trip->driver_id || (int) $user->id === (int) $trip->child->parent_id;
});

Broadcast::channel('parents.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

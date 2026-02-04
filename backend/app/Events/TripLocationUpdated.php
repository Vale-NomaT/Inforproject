<?php

namespace App\Events;

use App\Models\Trip;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TripLocationUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public Trip $trip;

    public float $lat;

    public float $lng;

    public function __construct(Trip $trip, float $lat, float $lng)
    {
        $this->trip = $trip;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('trips.'.$this->trip->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'trip.location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'trip_id' => $this->trip->id,
            'child_id' => $this->trip->child_id,
            'driver_id' => $this->trip->driver_id,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}

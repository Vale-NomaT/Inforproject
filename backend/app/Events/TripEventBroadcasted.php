<?php

namespace App\Events;

use App\Models\TripEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TripEventBroadcasted implements ShouldBroadcast
{
    use SerializesModels;

    public TripEvent $event;

    public function __construct(TripEvent $event)
    {
        $this->event = $event;
    }

    public function broadcastOn(): array
    {
        $trip = $this->event->trip;

        return [
            new PrivateChannel('parents.'.$trip->child->parent_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'trip.event';
    }

    public function broadcastWith(): array
    {
        $trip = $this->event->trip;

        return [
            'trip_id' => $trip->id,
            'child_id' => $trip->child_id,
            'driver_id' => $trip->driver_id,
            'type' => $this->event->type,
            'created_at' => $this->event->created_at?->toIso8601String(),
            'lat' => $this->event->lat,
            'lng' => $this->event->lng,
        ];
    }
}

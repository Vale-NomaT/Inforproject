<?php

namespace App\Events;

use App\Models\BookingRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public BookingRequest $booking;

    public function __construct(BookingRequest $booking)
    {
        $this->booking = $booking;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('parents.'.$this->booking->parent_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'child_id' => $this->booking->child_id,
            'driver_id' => $this->booking->driver_id,
            'status' => $this->booking->status,
        ];
    }
}

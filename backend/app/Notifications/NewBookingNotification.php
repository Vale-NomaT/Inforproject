<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $parentName,
        public string $childName,
        public int $bookingId
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_booking',
            'title' => 'New Booking Request',
            'message' => "{$this->parentName} has requested a booking for {$this->childName}.",
            'booking_id' => $this->bookingId,
            'url' => '/driver/bookings', // Redirect to driver bookings
            'icon' => 'user-plus',
            'color' => 'text-purple-500',
        ];
    }
}

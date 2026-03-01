<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $status,
        public string $driverName,
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
        $color = $this->status === 'approved' ? 'text-green-500' : 'text-red-500';
        $icon = $this->status === 'approved' ? 'check-circle' : 'x-circle';

        return [
            'type' => 'booking_status',
            'title' => 'Booking ' . ucfirst($this->status),
            'message' => "Your booking request with {$this->driverName} has been {$this->status}.",
            'booking_id' => $this->bookingId,
            'url' => '/parent/dashboard', // Redirect to dashboard
            'icon' => $icon,
            'color' => $color,
        ];
    }
}

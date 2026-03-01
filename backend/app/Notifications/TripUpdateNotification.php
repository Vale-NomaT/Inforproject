<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TripUpdateNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $message,
        public int $tripId,
        public string $childName
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
            'type' => 'trip_update',
            'title' => 'Trip Update: ' . $this->childName,
            'message' => $this->message,
            'trip_id' => $this->tripId,
            'url' => '/parent/live-trips', // Hardcoded for now to avoid route issues in different contexts
            'icon' => 'truck', // For UI display
            'color' => 'text-blue-500', // For UI display
        ];
    }
}

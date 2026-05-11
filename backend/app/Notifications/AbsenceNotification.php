<?php

namespace App\Notifications;

use App\Models\TripAbsence;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AbsenceNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TripAbsence $absence,
        public string      $childName,
        public bool        $isCancellation = true   // false = reinstatement
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $dateRange = $this->absence->start_date->isSameDay($this->absence->end_date)
            ? $this->absence->start_date->format('D d M Y')
            : $this->absence->start_date->format('d M') . ' – ' . $this->absence->end_date->format('d M Y');

        if ($this->isCancellation) {
            $title   = "Absence: {$this->childName}";
            $message = "{$this->childName} will not be available on {$dateRange} ({$this->absence->runLabel()} run).";
            if ($this->absence->reason) {
                $message .= " Reason: {$this->absence->reason}.";
            }
            $icon  = 'alert-circle';
            $color = 'text-amber-500';
        } else {
            $title   = "Absence Cancelled: {$this->childName}";
            $message = "{$this->childName}'s absence for {$dateRange} has been cancelled. They will be available as scheduled.";
            $icon  = 'check-circle';
            $color = 'text-green-500';
        }

        return [
            'type'       => 'absence',
            'title'      => $title,
            'message'    => $message,
            'absence_id' => $this->absence->id,
            'url'        => '/driver/trips',
            'icon'       => $icon,
            'color'      => $color,
        ];
    }
}

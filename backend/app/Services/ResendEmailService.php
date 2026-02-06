<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class ResendEmailService
{
    /**
     * Send an email using the configured Laravel Mail driver.
     * Note: This service previously used Resend directly but has been refactored
     * to use the standard Mail facade, allowing for SMTP/PHPMailer usage.
     */
    public function send(string $to, string $subject, string $html): void
    {
        Mail::html($html, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject);
        });
    }
}

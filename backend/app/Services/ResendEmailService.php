<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ResendEmailService
{
    public function send(string $to, string $subject, string $html): void
    {
        $apiKey = Config::get('services.resend.key');

        if (! $apiKey) {
            return;
        }

        $fromAddress = Config::get('mail.from.address', 'no-reply@safekids.test');
        $fromName = Config::get('mail.from.name', 'SafeRide Kids');

        Http::withoutVerifying()->withToken($apiKey)->post('https://api.resend.com/emails', [
            'from' => $fromName.' <'.$fromAddress.'>',
            'to' => [$to],
            'subject' => $subject,
            'html' => $html,
        ]);
    }
}

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('trips:schedule-next-school-day')
    ->dailyAt('06:00')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Scheduled Task Success: trips:schedule-next-school-day');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled Task Failed: trips:schedule-next-school-day');
    });

Schedule::command('scores:calculate-driver-performance')
    ->dailyAt('03:00')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Scheduled Task Success: scores:calculate-driver-performance');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled Task Failed: scores:calculate-driver-performance');
    });

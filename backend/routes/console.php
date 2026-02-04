<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('trips:schedule-next-school-day')->dailyAt('06:00');
Schedule::command('scores:calculate-driver-performance')->dailyAt('03:00');

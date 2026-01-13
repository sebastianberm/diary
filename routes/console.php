<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:sync-immich')->hourly();

// Schedule Inactivity Reminders (e.g. at 19:00)
Schedule::command('app:send-inactivity-reminders')->dailyAt('19:00');

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('app:check-daily-sensus')->dailyAt('23:00');
Schedule::command('app:check-user-sensus')->dailyAt('00:00');

// Command worker untuk menjalankan manual jika diperlukan
Artisan::command('worker', function () {
    $this->call('app:check-daily-sensus');
    $this->call('app:check-user-sensus');
    $this->info('Both sensus commands executed successfully!');
})->purpose('Manually run daily and user sensus checks');

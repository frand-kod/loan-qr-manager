<?php

use App\Console\Commands\SendDebtReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// WA Reminder
Schedule::command(SendDebtReminders::class)->dailyAt('08:00');

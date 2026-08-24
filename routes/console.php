<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| Runs via a single system cron entry (see docs/security/deployment-checklist.md):
|     * * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
|
| Nothing here is time-critical; all of it is housekeeping.
*/

// Nightly: null/delete expired verification and password-reset tokens across
// all four portals. Keeps the security-token columns from accumulating.
Schedule::command('tokens:prune-expired')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Laravel's built-in broker cleanup, as a belt-and-braces second pass over the
// password_reset_tokens table.
Schedule::command('auth:clear-resets')
    ->daily()
    ->withoutOverlapping();

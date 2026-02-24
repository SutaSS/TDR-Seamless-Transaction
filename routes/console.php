<?php

use App\Jobs\AutoCompleteOrders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled tasks

// auto-complete orders that have been shipped for more than 7 days
Schedule::job(new AutoCompleteOrders)->hourly();

// prune old models (e.g. old password resets, old activity logs)
Schedule::command('model:prune')->daily();

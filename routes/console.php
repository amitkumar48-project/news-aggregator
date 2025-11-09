<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule the command every 10 minutes to fetch the latest news, without overlapping:
Schedule::command('news:fetch')->everyTenMinutes()->withoutOverlapping()->onOneServer();
<?php

use App\Console\Commands\CheckSslRenewalsSchedulesCommand;
use App\Console\Commands\ProcessProxySchedulesCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule LaLiga:
Schedule::command('app:process-proxy-schedules-command')->everyFifteenSeconds();

// Schedule SSL:
// Schedule::command('app:check-ssl-renewals-schedules-command')->everyMinute();

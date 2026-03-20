<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ajustar los tiempos de ejecucion cuando se suba a prod
// Schedule Manual LaLiga:
Schedule::command('app:process-proxy-schedules-command')->everyMinute();

// Schedule SSL:
Schedule::command('app:check-ssl-renewals-schedules-command')->everyMinute();

// Schedule Automático Match:
Schedule::command('app:add-automatic-schedule-match-command')->everyMinute();

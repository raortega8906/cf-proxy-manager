<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ajustar tiempos de ejecución en las pruebas

// Schedule Manual LaLiga:
Schedule::command('app:process-proxy-schedules-command')->everyMinute();

// Schedule SSL:
Schedule::command('app:check-ssl-renewals-schedules-command')->everyMinute();

// Schedule Automático Match:
// Descomentar y eliminar el de abajo cuando se terminen las pruebas
// schedule::command('app:add-automatic-schedule-match-command')->daily();
Schedule::command('app:add-automatic-schedule-match-command')->dailyAt('03:02');

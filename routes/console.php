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
schedule::command('app:add-automatic-schedule-match-command')->dailyAt('00:00');

// Schedule Buscar SSL automático de Sitios:
Schedule::command('app:check-site-ssl-command')->dailyAt('00:05');

// Schedule para obtener el estado de los proxys: (Dos minutos para si se interactúa con cloudflare se sincronice en la app)
Schedule::command('app:sync-proxy-status-command')->everyTwoMinutes();

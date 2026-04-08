<?php

namespace App\Console\Commands;

use App\Models\ProxySchedule;
use App\Services\CloudflareService;
use App\Services\ProxyLogService;
use Illuminate\Console\Command;

class ProcessProxySchedulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-proxy-schedules-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function handle(CloudflareService $cloudflare, ProxyLogService $proxyLog)
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Procesando schedules de proxies...');

        $schedules = ProxySchedule::where('type', 'laliga_match')
            ->whereIn('status', ['pending', 'active'])
            ->get();

        if ($schedules->isEmpty()) {
            $this->line('  → Sin schedules para procesar.');
            return self::SUCCESS;
        }

        foreach ($schedules as $schedule) {

            $sites = $schedule->sites;

            // Desactivar el proxy
            if ($schedule->status === 'pending' && $schedule->disable_at <= now()) {

                $this->line(" ↓ Desactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if ($site->proxy_enabled) {

                        // Revisar si la liga bloquea el rango de Cloudflare
                        $isBlocked = $cloudflare->isBlockedByLaliga($site);

                        if (!$isBlocked) {
                            $this->line("    · {$site->domain} → NO AFECTADO, se omite");
                            $proxyLog->writeLogs($site, 'proxy_disabled', 'laliga', true, 'Dominio no afectado por bloqueo, se omite desactivación');
                            continue;
                        }

                        $ok = $cloudflare->setProxyStatus($site);
                        $proxyLog->writeLogs($site, 'proxy_disabled', 'laliga', $ok, 'Desactivación por schedule LaLiga');

                        $this->line("    · {$site->domain} → " . ($ok ? 'SUCCESS' : 'ERROR'));
                    }
                }

                $schedule->update(['status' => 'active']);

            // Reactivar el proxy
            } elseif ($schedule->status === 'active' && $schedule->enable_at <= now()) {

                $this->line(" ↑ Reactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if (!$site->proxy_enabled) {

                        $ok = $cloudflare->setProxyStatus($site);
                        $proxyLog->writeLogs($site, 'proxy_enabled', 'laliga', $ok, 'Activación por schedule LaLiga');

                        $this->line("    · {$site->domain} → " . ($ok ? 'SUCCESS' : 'ERROR'));
                    }
                }

                $schedule->update(['status' => 'completed']);

            } else {
                $this->line(" · Esperando: {$schedule->description} (disable_at: {$schedule->disable_at})");
            }
        }

        $this->info('Listo.');

        return self::SUCCESS;
    }
}

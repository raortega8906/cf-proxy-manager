<?php

namespace App\Console\Commands;

use App\Models\ProxyLog;
use App\Models\ProxySchedule;
use App\Models\ProxySite;
use App\Services\CloudflareService;
use App\Services\ProxyLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Procesando schedules...');
        
        $schedules = ProxySchedule::all();

        if ($schedules->isEmpty()) {

            $this->line('  → Sin schedules para desactivar.');

            return;

        }

        foreach($schedules as $schedule) {

            if ($schedule->type === 'ssl_renewal' || $schedule->type === 'manual') {
                continue;
            }

            if ($schedule->status === 'failed' || $schedule->status === 'completed') {
                continue;
            }

            $sites = ProxySite::whereIn('id', $schedule->site_ids)->get();

            // Dactivar el proxy
            if ($schedule->status === 'pending' && $schedule->disable_at <= now()) {

                $this->line(" ↓ Desactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if ($site->proxy_enabled) {

                        $ok = $cloudflare->setProxyStatus($site, true);

                        $proxyLog->writeLogs($site, 'proxy_disabled', 'laliga', 'success', 'Desactivación por schedule La liga');

                        $this->line("    · {$site->domain} → " . ($ok ? 'OK' : 'ERROR'));

                    } else {
                        continue;
                    }
                }

                $schedule->update(['status' => 'active']);

            // Reactivar el proxy
            } elseif ($schedule->status === 'active' && $schedule->enable_at <= now()) {

                $this->line(" ↑ Reactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if (!$site->proxy_enabled) {

                        $ok = $cloudflare->setProxyStatus($site, true);

                        $proxyLog->writeLogs($site, 'proxy_enabled', 'laliga', 'success', 'Activación por schedule La liga');
                        
                        $this->line("    · {$site->domain} → " . ($ok ? 'OK' : 'ERROR'));

                    } else {
                        continue;
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

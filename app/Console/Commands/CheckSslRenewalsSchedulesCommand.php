<?php

namespace App\Console\Commands;

use App\Models\ProxySchedule;
use App\Models\ProxySite;
use App\Services\CloudflareService;
use App\Services\ProxyLogService;
use App\Services\ProxyScheduleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSslRenewalsSchedulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-ssl-renewals-schedules-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(CloudflareService $cloudflare, ProxyLogService $proxyLog, ProxyScheduleService $proxySchedule)
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Procesando schedules...');
        
        $schedules = ProxySchedule::all();

        if ($schedules->isEmpty()) {

            $this->line('  → Sin schedules para desactivar.');

            return;

        }

        foreach($schedules as $schedule) {

            if ($schedule->type === 'laliga_match' || $schedule->type === 'manual') {
                continue;
            }

            if ($schedule->status === 'failed' || $schedule->status === 'completed') {
                continue;
            }

            $sites = ProxySite::whereIn('id', $schedule->site_ids)->get();

            // Desactivar el proxy
            if ($schedule->status === 'pending' && $schedule->disable_at <= now()) {

                $this->line(" ↓ Desactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if ($site->proxy_enabled) {

                        $ok = $cloudflare->setProxyStatus($site, true);
                        $proxyLog->writeLogs($site, 'proxy_disabled', 'ssl_renewal', $ok, 'Desactivación por schedule SSL');

                        $this->line("    · {$site->domain} → " . ($ok ? 'SUCCESS' : 'ERROR'));

                    } else {
                        continue;
                    }
                }

                $schedule->update(['status' => 'active']);

            // Reactivar el proxy
            } elseif ($schedule->status === 'active' && $schedule->enable_at <= now()) {

                $this->line(" ↑ Reactivando proxy: {$schedule->description}");

                $date_ssl_next_renewal = null;

                foreach ($sites as $site) {
                    if (!$site->proxy_enabled) {

                        $date_ssl_next_renewal = now()->addMonths(3)->format('Y-m-d');

                        $site->update(['ssl_next_renewal' => $date_ssl_next_renewal]);

                        $ok = $cloudflare->setProxyStatus($site, true);
                        $proxyLog->writeLogs($site, 'proxy_enabled', 'ssl_renewal', $ok, 'Activación por schedule SSL');

                        $this->line("    · {$site->domain} → " . ($ok ? 'SUCCESS' : 'ERROR'));
                    } else {
                        continue;
                    }
                }

                $schedule->update([
                    'status'     => 'completed',
                ]);

                $baseDate = now()->addMonths(3);
                $proxySchedule->writeAutomaticSchedule(
                    'ssl_renewal', 
                    'Automático', 
                    $baseDate->copy()->startOfDay(),
                    $baseDate->copy()->setTime(9, 0),
                    'pending',
                    $schedule->site_ids
                );

            } else {
                $this->line(" · Esperando: {$schedule->description} (disable_at: {$schedule->disable_at})");
            }

        }

        $this->info('Listo.');

        return self::SUCCESS;
    }
}

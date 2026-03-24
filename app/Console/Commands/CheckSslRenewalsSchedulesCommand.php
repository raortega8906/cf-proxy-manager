<?php

namespace App\Console\Commands;

use App\Models\ProxySchedule;
use App\Services\CloudflareService;
use App\Services\ProxyLogService;
use Illuminate\Console\Command;

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
    public function handle(CloudflareService $cloudflare, ProxyLogService $proxyLog)
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Procesando schedules de renovación SSL...');

        $schedules = ProxySchedule::where('type', 'ssl_renewal')
            ->whereIn('status', ['pending', 'active'])
            ->get();

        if ($schedules->isEmpty()) {
            $this->line('  → Sin schedules SSL para procesar.');
            return self::SUCCESS;
        }

        foreach ($schedules as $schedule) {

            $sites = $schedule->sites;

            if ($schedule->status === 'pending' && $schedule->disable_at <= now()) {

                $this->line(" ↓ Desactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if ($site->proxy_enabled) {
                        $ok = $cloudflare->setProxyStatus($site);
                        $proxyLog->writeLogs($site, 'proxy_disabled', 'ssl_renewal', $ok, 'Desactivación por schedule SSL');
                        $this->line("    · {$site->domain} → " . ($ok ? 'SUCCESS' : 'ERROR'));
                    }
                }

                $schedule->update(['status' => 'active']);

            } elseif ($schedule->status === 'active' && $schedule->enable_at <= now()) {

                $this->line(" ↑ Reactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if (!$site->proxy_enabled) {
                        $ok = $cloudflare->setProxyStatus($site);
                        $proxyLog->writeLogs($site, 'proxy_enabled', 'ssl_renewal', $ok, 'Activación por schedule SSL');
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

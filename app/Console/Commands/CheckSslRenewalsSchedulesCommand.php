<?php

namespace App\Console\Commands;

use App\Models\ProxyLog;
use App\Models\ProxySchedule;
use App\Models\ProxySite;
use App\Services\CloudflareService;
use Illuminate\Console\Command;
use Carbon\Carbon;
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
    public function handle(CloudflareService $cloudflare)
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

            // Ha llegado la hora de DESactivar el proxy
            if ($schedule->status === 'pending' && $schedule->disable_at <= now()) {

                $this->line(" ↓ Desactivando proxy: {$schedule->description}");

                foreach ($sites as $site) {
                    if ($site->proxy_enabled) {

                        $ok = $cloudflare->setProxyStatus($site, true);

                        ProxyLog::create([
                            'action' => 'proxy_disabled',
                            'reason' => 'ssl_renewal',
                            'status' => 'success',
                            'message' => 'Desactivación por schedule La liga', 
                            'site_id' => $site->id
                        ]);

                        $this->line("    · {$site->domain} → " . ($ok ? 'OK' : 'ERROR'));

                    } else {
                        continue;
                    }
                }

                $schedule->update(['status' => 'active']);

            // Ha llegado la hora de REactivar el proxy
            } elseif ($schedule->status === 'active' && $schedule->enable_at <= now()) {

                $this->line(" ↑ Reactivando proxy: {$schedule->description}");

                $date_ssl_next_renewal = null;

                foreach ($sites as $site) {
                    if (!$site->proxy_enabled) {
                        $date_ssl_next_renewal = now()->addMonths(3)->format('Y-m-d');
                        
                        $site->update(['ssl_next_renewal' => $date_ssl_next_renewal]);
                        
                        $ok = $cloudflare->setProxyStatus($site, true);

                        ProxyLog::create([
                            'action' => 'proxy_enabled',
                            'reason' => 'ssl_renewal',
                            'status' => 'success',
                            'message' => 'Activación por schedule La liga', 
                            'site_id' => $site->id
                        ]);

                        $this->line("    · {$site->domain} → " . ($ok ? 'OK' : 'ERROR'));
                    } else {
                        continue;
                    }
                }

                $schedule->update([
                    'status'     => 'completed',
                ]);

                ProxySchedule::create([
                    'type'        => 'ssl_renewal',
                    'description' => 'Automático',
                    'disable_at'  => now()->addMonths(3)->format('Y-m-d') . ' 00:00:00',
                    'enable_at'   => now()->addMonths(3)->format('Y-m-d') . ' 09:00:00',
                    'status'      => 'pending',
                    'site_ids'    => $schedule->site_ids,
                ]);

            } else {
                $this->line(" · Esperando: {$schedule->description} (disable_at: {$schedule->disable_at})");
            }

        }

        $this->info('Listo.');

        return self::SUCCESS;
    }
}

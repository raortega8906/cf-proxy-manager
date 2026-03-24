<?php

namespace App\Console\Commands;

use App\Models\ProxySite;
use App\Services\ProxyScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSiteSslCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-site-ssl-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ProxyScheduleService $proxySchedule)
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Comprobando renovaciones SSL...');

        $sites = ProxySite::where('ssl_auto_renewal', true)->latest()->get();

        if ($sites->isEmpty()) {
            $this->line('  → Sin sitios con SSL automático configurado.');
            return self::SUCCESS;
        }

        $today = Carbon::today();
        $sites_ids = [];

        foreach ($sites as $site) {

            if (!$site->ssl_next_renewal->isToday()) {
                $this->line("  · {$site->domain} → próxima renovación {$site->ssl_next_renewal->format('d/m/Y')}");
                continue;
            }

            $sites_ids[] = $site->id;
            $site->ssl_next_renewal = now()->addMonths(3);
            $site->save();

            $this->line("  ✓ {$site->domain} → próxima renovación: {$site->ssl_next_renewal->format('d/m/Y')}");
        }

        if (empty($sites_ids)) {
            $this->line('  → Ningún sitio renueva hoy.');
            return self::SUCCESS;
        }

        $this->line("  ↓ Creando schedule SSL Automático");
        Log::error("  ↓ Creando schedule SSL Automático");

        $proxySchedule->writeAutomaticSchedule(
            'ssl_renewal',
            'Automático',
            $today->copy()->setTime(2, 0),
            $today->copy()->setTime(8, 0),
            'pending',
            $sites_ids
         );

        $this->info('Listo.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\ProxySite;
use App\Services\CloudflareService;
use Illuminate\Console\Command;

class SyncProxyStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-proxy-status-command';

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
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Sincronizando estado de proxies...');

        $sites = ProxySite::latest()->get();

        foreach ($sites as $site) {
            $cloudflare->syncSiteStatus($site);
        }

        $this->info('Listo.');

        return self::SUCCESS;
    }
}

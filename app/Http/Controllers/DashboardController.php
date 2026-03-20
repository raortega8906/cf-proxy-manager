<?php

namespace App\Http\Controllers;

use App\Models\ProxyLog;
use App\Models\ProxySchedule;
use App\Models\ProxySite;
use App\Services\CloudflareService;
use App\Services\LaligaService;
use App\Services\ProxyLogService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

use function PHPUnit\Framework\isEmpty;

class DashboardController extends Controller
{
    private CloudflareService $cloudflare;
    private ProxyLogService $proxyLog;

    public function __construct( CloudflareService $cloudflare, ProxyLogService $proxyLog ) {
        $this->cloudflare = $cloudflare;
        $this->proxyLog = $proxyLog;
    }

    public function dashboard(): View
    {
        $sites = ProxySite::all();
        $schedules = ProxySchedule::all();
        $logs = ProxyLog::paginate(10);

        $countEnabled = $sites->where('proxy_enabled', true)->count();
        $countLaLiga = $sites->where('affected_by_laliga', true)->count();
        $countSsl = $sites->where('ssl_auto_renewal', true)->count();
        $countSchedulePending =  $schedules->where('status', 'pending')->count();
        $schedulePendingActive = ProxySchedule::whereIn('status', ['pending', 'active'])->get();

        foreach ($sites as $site) {
            $this->cloudflare->syncSiteStatus($site);
        }

        return view('dashboard', compact('sites', 'logs', 'schedulePendingActive', 'schedules', 'countEnabled', 'countLaLiga', 'countSsl', 'countSchedulePending'));
    }

    public function activateOrDeactivateProxy(ProxySite $site): RedirectResponse
    {
        $enabled = true;
        $action = '';
        $message = '';
        
        $response = $this->cloudflare->setProxyStatus($site, $enabled);

        if ( $site->proxy_enabled ) {
            $action = 'proxy_enabled';
            $message = 'Activación manual del proxy';
        } else {
            $action = 'proxy_disabled';
            $message = 'Desactivación manual del proxy';
        }

        if ($response) {

            $this->proxyLog->writeLogs($site, $action, 'manual', 'success', $message);

            return redirect()->back()->with('success', 'Proxy cambiado correctamente en Cloudflare.');

        }

        $this->proxyLog->writeLogs($site, $action, 'manual', 'error', $message);

        return redirect()->back()->withErrors(['error' => 'No se pudo cambiar el estado del proxy en Cloudflare.']);
    }

    public function activateProxyAll(): RedirectResponse
    {
        $response = $this->cloudflare->activateProxyStatusAll();

        if ($response) {

            // $this->proxyLog->writeLogs($site, 'proxy_enabled', 'manual', 'success', 'Activación masiva');

            return redirect()->back()->with('success', 'Todos los proxies han sido activados correctamente en Cloudflare.');
        }

        // $this->proxyLog->writeLogs($site, 'proxy_enabled', 'manual', 'error', 'Activación masiva');

        return redirect()->back()->withErrors(['error' => 'No se pudieron activar todos los proxies en Cloudflare.']);
    }

    public function deactivateProxyAll(): RedirectResponse
    {
        $response = $this->cloudflare->deactivateProxyStatusAll();

        if ($response) {

            // $this->proxyLog->writeLogs($site, 'proxy_disabled', 'manual', 'success', 'Desactivación masiva');

            return redirect()->back()->with('success', 'Todos los proxies han sido desactivados correctamente en Cloudflare.');
        }

        // $this->proxyLog->writeLogs($site, 'proxy_disabled', 'manual', 'error', 'Desactivación masiva');

        return redirect()->back()->withErrors(['error' => 'No se pudieron desactivar todos los proxies en Cloudflare.']);
    }
}
 
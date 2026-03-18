<?php

namespace App\Http\Controllers;

use App\Models\ProxyLog;
use App\Models\ProxySchedule;
use App\Models\ProxySite;
use App\Services\CloudflareService;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    private CloudflareService $cloudflare;

    public function __construct(CloudflareService $cloudflare)
    {
        $this->cloudflare = $cloudflare;
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

        // dd($schedulePendingActive);

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

            ProxyLog::create([
                'action' => $action,    // proxy_enabled | proxy_disabled
                'reason' => 'manual',    // laliga | ssl_renewal | manual
                'status' => 'success',    // success | error
                'message' => $message, 
                'site_id' => $site->id
            ]);

            return redirect()->back()->with('success', 'Proxy cambiado correctamente en Cloudflare.');

        }

        ProxyLog::create([
            'action' => $action,    // proxy_enabled | proxy_disabled
            'reason' => 'manual',    // laliga | ssl_renewal | manual
            'status' => 'error',    // success | error
            'message' => $message, 
            'site_id' => $site->id
        ]);

        return redirect()->back()->withErrors(['error' => 'No se pudo cambiar el estado del proxy en Cloudflare.']);
    }

    public function activateProxyAll(): RedirectResponse
    {
        $response = $this->cloudflare->activateProxyStatusAll();

        if ($response) {

            ProxyLog::create([
                'action' => 'proxy_enabled',    // proxy_enabled | proxy_disabled
                'reason' => 'manual',    // laliga | ssl_renewal | manual
                'status' => 'success',    // success | error
                'message' => 'Activación masiva', 
                'site_id' => '12'
            ]);

            return redirect()->back()->with('success', 'Todos los proxies han sido activados correctamente en Cloudflare.');
        }

        ProxyLog::create([
            'action' => 'proxy_enabled',    // proxy_enabled | proxy_disabled
            'reason' => 'manual',    // laliga | ssl_renewal | manual
            'status' => 'error',    // success | error
            'message' => 'Activación masiva', 
            'site_id' => '12'
        ]);

        return redirect()->back()->withErrors(['error' => 'No se pudieron activar todos los proxies en Cloudflare.']);
    }

    public function deactivateProxyAll(): RedirectResponse
    {
        $response = $this->cloudflare->deactivateProxyStatusAll();

        if ($response) {

            ProxyLog::create([
                'action' => 'proxy_disabled',    // proxy_enabled | proxy_disabled
                'reason' => 'manual',    // laliga | ssl_renewal | manual
                'status' => 'success',    // success | error
                'message' => 'Desactivación masiva', 
                'site_id' => '12'
            ]);

            return redirect()->back()->with('success', 'Todos los proxies han sido desactivados correctamente en Cloudflare.');
        }

        ProxyLog::create([
            'action' => 'proxy_disabled',    // proxy_enabled | proxy_disabled
            'reason' => 'manual',    // laliga | ssl_renewal | manual
            'status' => 'error',    // success | error
            'message' => 'Desactivación masiva', 
            'site_id' => '12'
        ]);

        return redirect()->back()->withErrors(['error' => 'No se pudieron desactivar todos los proxies en Cloudflare.']);
    }
}
 
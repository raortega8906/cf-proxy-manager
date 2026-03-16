<?php

namespace App\Http\Controllers;

use App\Models\ProxySite;
use App\Services\CloudflareService;
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

        $countEnabled = $sites->where('proxy_enabled', true)->count();
        $countLaLiga = $sites->where('affected_by_laliga', true)->count();
        $countSsl = $sites->where('ssl_auto_renewal', true)->count();

        foreach ($sites as $site) {
            $this->cloudflare->syncSiteStatus($site);
        }

        return view('dashboard', compact('sites', 'countEnabled', 'countLaLiga', 'countSsl'));
    }

    public function activateOrDeactivateProxy(ProxySite $site): RedirectResponse
    {
        $enabled = true;
        
        $response = $this->cloudflare->setProxyStatus($site, $enabled);

        if ($response) {
            return redirect()->back()->with('success', 'Proxy cambiado correctamente en Cloudflare.');
        }

        return redirect()->back()->withErrors(['error' => 'No se pudo cambiar el estado del proxy en Cloudflare.']);
    }
}
 
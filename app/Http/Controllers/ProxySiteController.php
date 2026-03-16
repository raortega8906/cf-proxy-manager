<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProxySiteRequest;
use App\Http\Requests\UpdateProxySiteRequest;
use App\Models\ProxySite;
use App\Services\CloudflareService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProxySiteController extends Controller
{
    private CloudflareService $cloudflare;

    public function __construct(CloudflareService $cloudflare)
    {
        $this->cloudflare = $cloudflare;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sites = ProxySite::all();

        foreach ($sites as $site) {
            $this->cloudflare->syncSiteStatus($site);
        }

        return view('sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('sites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProxySiteRequest $request): RedirectResponse
    {

        $data = $request->validated();

        $domain = $data['domain'];
        $zone_id = $data['cloudflare_zone_id'];
        $response = $this->cloudflare->getDnsRecordAll($zone_id);

        if (!$response) {
            return redirect()->back()->withErrors(['cloudflare_zone_id' => 'No se pudo obtener los DNS records de Cloudflare. Verifica la zona y tu conexión con la API.']);
        }

        foreach ($response as $res) {

            if ($res['name'] === $domain) {
                $proxy_enabled = $res['proxied'];
                $record_id = $res['id'];
                break;
            } else {
                return redirect()->back()->withErrors(['domain' => 'No se encontró un DNS record para este dominio en la zona especificada.']);
            }

        }

        $data['cloudflare_dns_record_id'] = $record_id;
        $data['proxy_enabled'] = $proxy_enabled;

        // Testing data before saving
        // dd($data);

        ProxySite::create($data);
        
        return redirect()->route('sites.index')->with('success', 'Sitio creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProxySite $proxySite): View
    {
        return view('sites.edit', compact('proxySite'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProxySiteRequest $request, ProxySite $proxySite)
    {
        $data = $request->validated();

        // Los checkboxes no se envían si no están marcados, forzamos el valor
        $data['ssl_auto_renewal']   = $request->boolean('ssl_auto_renewal');
        $data['affected_by_laliga'] = $request->boolean('affected_by_laliga');

        // Si desmarcaron SSL, limpiamos la fecha
        if (!$data['ssl_auto_renewal']) {
            $data['ssl_next_renewal'] = null;
        }

        $proxySite->update($data);

        return redirect()->route('sites.index')->with('success', 'Sitio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProxySite $proxySite): RedirectResponse
    {
        $proxySite->delete();

        return redirect()->route('sites.index')->with('success', 'Sitio eliminado correctamente.');
    }

    public function activateOrDesactivateProxy(ProxySite $site): RedirectResponse
    {
        $enabled = true;
        
        $response = $this->cloudflare->setProxyStatus($site, $enabled);

        if ($response) {
            return redirect()->back()->with('success', 'Proxy cambiado correctamente en Cloudflare.');
        }

        return redirect()->back()->withErrors(['error' => 'No se pudo cambiar el estado del proxy en Cloudflare.']);
    }
}

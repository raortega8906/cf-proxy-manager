<?php

namespace App\Services;

use App\Models\ProxySite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Este servicio se encarga de interactuar con la API de Cloudflare
// para gestionar los DNS records y su estado de proxy (nube naranja).
// También se encarga de registrar en ProxyLog cada cambio realizado.

class CloudflareService {

    private string $api_url;
    private array $headers;

    public function __construct()
    {
        $this->api_url = config('cloudflare.api_url');
        $this->headers = [
            'Authorization' => 'Bearer ' . config('cloudflare.api_token'),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Obtiene el estado actual de un DNS record en Cloudflare.
     */

    public function getDnsRecord(string $zone_id, string $record_id): ?array
    {
        $response = Http::withHeaders($this->headers)
            ->get("{$this->api_url}/zones/{$zone_id}/dns_records/{$record_id}");

        if($response->successful()) {
            return $response->json('result');
        } else {

            // Loguear error para debugging
            Log::error('[Cloudflare] getDnsRecord failed', [
                'zone_id' => $zone_id,
                'record_id' => $record_id,
                'response' => $response->json(),
            ]);

            return null;

        }
    }

    /**
     * Obtiene el estado actual de todos los DNS record en Cloudflare.
     */

    public function getDnsRecordAll(string $zone_id): ?array
    {
        $response = Http::withHeaders($this->headers)
            ->get("{$this->api_url}/zones/{$zone_id}/dns_records/");

        if($response->successful()) {
            return $response->json('result');
        } else {

            // Loguear error para debugging
            Log::error('[Cloudflare] getDnsRecord failed', [
                'zone_id' => $zone_id,
                'response' => $response->json(),
            ]);

            return null;

        }
    }

    /**
     * Obtener el dato de proxied en todos los DNS records.
     */

    public function syncSiteStatus(ProxySite $site): void
    {
        $record_id = $site->cloudflare_dns_record_id;
        $zone_id = $site->cloudflare_zone_id;

        $record = $this->getDnsRecord($zone_id, $record_id);

        if ($record) {

            $site->proxy_enabled = $record['proxied'];
            $site->save();

        } else {
            Log::error("[Cloudflare] syncSiteStatus failed for site ID {$site->id}: No se pudo obtener el DNS record.");
        }
    }

    /**
     * Comprueba si un dominio está afectado por el bloqueo de LaLiga.
     * Devuelve true si está bloqueado, false si responde con normalidad.
     */

    public function isBlockedByLaliga(ProxySite $site): bool
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get('http://' . $site->domain);

            return str_contains(
                $response->body(),
                'Liga Nacional de Fútbol Profesional'
            );

        } catch (\Exception $e) {
            Log::warning("[Cloudflare] isBlockedByLaliga check failed for {$site->domain}: {$e->getMessage()}");
            return false;
        }
    }

    // Probar cuando haya bloqueo:
//    public function isBlockedByLaliga(ProxySite $site): bool
//    {
//        try {
//            $response = Http::timeout(10)
//                ->withoutVerifying()
//                ->get('http://' . $site->domain);
//
//            $body = $response->body();
//            $status = $response->status();
//
//            // El bloqueo de LaLiga típicamente devuelve 451 (Unavailable For Legal Reasons)
//            // o en algunos casos 200 con página de bloqueo inyectada por el operador
//            $isLegalBlock = $status === 451;
//
//            // Señales específicas del bloqueo LaLiga (todas deben coincidir para mayor precisión)
//            $signals = [
//                str_contains($body, 'Liga Nacional de Fútbol Profesional'),
//                str_contains($body, 'Telefónica Audiovisual Digital') || str_contains($body, 'Telefonica Audiovisual Digital'),
//                str_contains($body, '1005/2024-H') || str_contains($body, 'Juzgado de lo Mercantil'),
//            ];
//
//            $matchedSignals = array_sum($signals); // Cuántas señales coinciden
//
//            // Consideramos bloqueado si:
//            // - Es un 451 explícito, O
//            // - Al menos 2 de las 3 señales específicas coinciden
//            return $isLegalBlock || $matchedSignals >= 2;
//
//        } catch (\Exception $e) {
//            Log::warning("[Cloudflare] isBlockedByLaliga check failed for {$site->domain}: {$e->getMessage()}");
//            return false;
//        }
//    }

     /**
     * Activa o desactiva el proxy (nube naranja) de un DNS record.
     *
     * Usa PATCH para modificar solo el campo `proxied`, sin tocar
     * el resto de campos del registro (name, content, ttl…).
     */

    public function setProxyStatus(ProxySite $site): bool
    {
        $enabled = true;
        $record_id = $site->cloudflare_dns_record_id;
        $zone_id = $site->cloudflare_zone_id;
        $proxy_enabled = $site->proxy_enabled;

        if ($proxy_enabled !== $enabled) {

            $response = Http::withHeaders($this->headers)
            ->patch("{$this->api_url}/zones/{$zone_id}/dns_records/{$record_id}", [
                'proxied' => $enabled
            ]);

        } else {

            $enabled = false;
            $response = Http::withHeaders($this->headers)
            ->patch("{$this->api_url}/zones/{$zone_id}/dns_records/{$record_id}", [
                'proxied' => $enabled
            ]);

        }

        if ($response->successful()) {

            $site->proxy_enabled = $enabled;
            $site->save();

            return true;

        } else {

            Log::error("[Cloudflare] setProxyStatus failed for site ID {$site->id}: No se pudo actualizar el DNS record.");

            return false;

        }
    }

     /**
     * Activa el proxy (nube naranja) de todos los DNS record.
     *
     * Usa PATCH para modificar solo el campo `proxied`, sin tocar
     * el resto de campos del registro (name, content, ttl…).
     */

    public function activateProxyStatusAll(): bool
    {
        $sites = ProxySite::all();

        foreach ($sites as $site) {

            $record_id = $site->cloudflare_dns_record_id;
            $zone_id = $site->cloudflare_zone_id;
            $proxy_enabled = $site->proxy_enabled;

            if ($proxy_enabled) {
                continue;
            }

            $response = Http::withHeaders($this->headers)
            ->patch("{$this->api_url}/zones/{$zone_id}/dns_records/{$record_id}", [
                'proxied' => true
            ]);

            if ($response->successful()) {

                $site->proxy_enabled = true;
                $site->save();

            } else {

                Log::error("[Cloudflare] activateProxyStatusAll failed for site ID {$site->id}: No se pudo actualizar el DNS record.");

                return false;

            }

        }
        return true;
    }

      /**
     * Desactivar el proxy (nube naranja) de todos los DNS record.
     *
     * Usa PATCH para modificar solo el campo `proxied`, sin tocar
     * el resto de campos del registro (name, content, ttl…).
     */

    public function deactivateProxyStatusAll(): bool
    {
        $sites = ProxySite::all();

        foreach ($sites as $site) {
            $record_id = $site->cloudflare_dns_record_id;
            $zone_id = $site->cloudflare_zone_id;
            $proxy_enabled = $site->proxy_enabled;

            if (!$proxy_enabled) {
                continue;
            }

            $response = Http::withHeaders($this->headers)
            ->patch("{$this->api_url}/zones/{$zone_id}/dns_records/{$record_id}", [
                'proxied' => false
            ]);

            if ($response->successful()) {

                $site->proxy_enabled = false;
                $site->save();

            } else {

                Log::error("[Cloudflare] deactivateProxyStatusAll failed for site ID {$site->id}: No se pudo actualizar el DNS record.");

                return false;

            }

        }
        return true;
    }

}

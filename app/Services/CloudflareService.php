<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    // Este servicio se encarga de interactuar con la API de Cloudflare
    // para gestionar los DNS records y su estado de proxy (nube naranja).
    // También se encarga de registrar en ProxyLog cada cambio realizado.

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

    // public function getDnsRecord(string $zone_id, string $record_id): ?array
    // {
    //     $response = Http::withHeaders($this->headers)
    //         ->get("{$this->api_url}/zones/{$zone_id}/dns_records/{$record_id}");

    //     if($response->successful()) {

    //         return $response->json('result');

    //     } else {

    //         // Loguear error para debugging
    //         Log::error('[Cloudflare] getDnsRecord failed', [
    //             'zone_id' => $zone_id,
    //             'record_id' => $record_id,
    //             'response' => $response->json(),
    //         ]);

    //         return null;

    //     }
    // }

    public function getDnsRecord(string $zone_id): ?array
    {
        $response = Http::withHeaders($this->headers)
            ->get("{$this->api_url}/zones/{$zone_id}/dns_records/");

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
     * Activa o desactiva el proxy (nube naranja) de un DNS record.
     *
     * Usa PATCH para modificar solo el campo `proxied`, sin tocar
     * el resto de campos del registro (name, content, ttl…).
     */
}
<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Este servicio se encarga de interactuar con la API de Football-data.org
// Solo usaremos el id de la liga para buscar partidos en el día

class LaligaService {

    private string $laliga_api_url;
    private array $headers;

    public function __construct()
    {
        $this->laliga_api_url = config('laliga.laliga_api_url');
        $this->headers = [
            'X-Auth-Token' => config('laliga.laliga_api_token'),
        ];
    }

    public function getMatches(Carbon $dateFrom, Carbon $dateTo): ?array
    {
        $day = '?dateFrom=' . $dateFrom->toDateString() . '&dateTo=' . $dateTo->toDateString();
        $response = Http::withHeaders($this->headers)->get($this->laliga_api_url . $day);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Matches failed', [

            'day_enter' => $day,
            'response' => $response->json()

        ]);

        return null;
    }

}

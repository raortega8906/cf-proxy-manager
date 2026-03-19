<?php

return [

    /*
    |--------------------------------------------------------------------------
    | football-data (Laliga API) Credentials
    |--------------------------------------------------------------------------
    | Genera tu token registrandote en: https://www.football-data.org/
    */

    'laliga_api_token' => env('LALIGA_API_TOKEN'),
    'laliga_api_url' => env('LALIGA_API', 'https://api.football-data.org/v4/competitions/PD/matches'),

];
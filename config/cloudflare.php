<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudflare API Credentials
    |--------------------------------------------------------------------------
    | Genera tu token en: Cloudflare → My Profile → API Tokens → Create Token
    | Permisos mínimos: Zone:DNS:Edit + Zone:Zone:Read
    */

    'api_token' => env('CLOUDFLARE_API_TOKEN'),
    'email' => env('CLOUDFLARE_EMAIL'),
    'api_url' => env('CLOUDFLARE_API', 'https://api.cloudflare.com/client/v4'),

    /*
    |--------------------------------------------------------------------------
    | SSL Renewal Settings
    |--------------------------------------------------------------------------
    | Ventana de tiempo que se desactiva el proxy para el certificado SSL se renueve correctamente
    | pueda completar el reto HTTP-01 sin que Cloudflare lo intercepte.
    */

    'ssl_renewal_downtime_minutes' => env('CF_SSL_DOWNTIME_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | LaLiga Match Settings
    |--------------------------------------------------------------------------
    | Margen antes y después de cada partido para desactivar/reactivar el proxy.
    */

    'match_pre_disable_minutes' => env('CF_MATCH_PRE_MINUTES', 15),
    'match_post_enable_minutes' => env('CF_MATCH_POST_MINUTES', 30),

];
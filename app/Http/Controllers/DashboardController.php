<?php

namespace App\Http\Controllers;

use App\Services\CloudflareService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard');
    }

    public function test()
    {
        $cloudflare = new CloudflareService();
        $zone_id = env('CLOUDFLARE_ZONE_ID');
        $name_server = 'test.caelix.es';
        $id = '';
        $proxy_enabled = 'true';

        $response = $cloudflare->getDnsRecord($zone_id);
        foreach ($response as $res)        {
            if ($res['name'] === $name_server) {
                $id = $res['id'];
                $proxy_enabled = $res['proxied'];
                dd($res, $id, $proxy_enabled);
            }
        }
    }
}
 
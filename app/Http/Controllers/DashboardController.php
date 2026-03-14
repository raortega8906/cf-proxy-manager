<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard');
    }

    public function test()
    {
        $response = Http::withToken(env('CLOUDFLARE_API_TOKEN'))
        ->get(env('CLOUDFLARE_API') . '/zones/' . env('CLOUDFLARE_ZONE_ID') . '/dns_records');
        dd($response);
    }
}
 
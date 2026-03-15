<?php

namespace App\Http\Controllers;

use App\Models\ProxySite;
use App\Services\CloudflareService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        $sites = ProxySite::all();

        return view('dashboard', compact('sites'));
    }
}
 
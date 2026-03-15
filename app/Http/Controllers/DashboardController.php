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
}
 
<?php

namespace App\Http\Controllers;

use App\Models\ProxyLog;
use Illuminate\Contracts\View\View;

class ProxyLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $logs = ProxyLog::all();

        return view('logs.index', compact('logs'));
    }
}

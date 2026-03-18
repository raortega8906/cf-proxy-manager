<?php

namespace App\Http\Controllers;

use App\Models\ProxyLog;
use App\Models\ProxySite;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProxyLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = ProxyLog::with('site')->latest();

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs  = $query->paginate(50)->withQueryString();
        $sites = ProxySite::all();

        return view('logs.index', compact('logs', 'sites'));
    }
}

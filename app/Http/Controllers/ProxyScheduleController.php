<?php

namespace App\Http\Controllers;

use App\Models\ProxySchedule;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ProxyScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $shedules = ProxySchedule::all();

        return view('schedules.index', compact('shedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('schedules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProxySchedule $proxySchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProxySchedule $proxySchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProxySchedule $proxySchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProxySchedule $proxySchedule)
    {
        //
    }
}

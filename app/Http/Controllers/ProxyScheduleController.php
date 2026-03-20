<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProxyScheduleRequest;
use App\Http\Requests\UpdateProxyScheduleRequest;
use App\Models\ProxySchedule;
use App\Models\ProxySite;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class ProxyScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $schedules = ProxySchedule::latest()->paginate(10);

        return view('schedules.index', compact('schedules'));
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
    public function store(StoreProxyScheduleRequest $request)
    {
        $validated = $request->validated();
        $validated['status']     = 'pending';
        $validated['disable_at'] = Carbon::parse($validated['disable_at']);
        $validated['enable_at']  = Carbon::parse($validated['enable_at']);

        if ($validated['type'] === 'laliga_match') {

            $site_ids = ProxySite::where('affected_by_laliga', true)->pluck('id')->toArray();

            if (empty($site_ids)) {
                return redirect()->back()->with('error', 'No hay dominios con LaLiga activado.');
            }

            $validated['site_ids'] = $site_ids;

        } elseif ($validated['type'] === 'ssl_renewal') {

            $site_ids = ProxySite::where('ssl_auto_renewal', true)->pluck('id')->toArray();

            if (empty($site_ids)) {
                return redirect()->back()->with('error', 'No hay dominios con renovación SSL automática activada.');
            }

            $validated['site_ids'] = $site_ids;
        }

        ProxySchedule::create($validated);

        return redirect()->route('schedules.index')->with('success', 'Schedule creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProxySchedule $proxySchedule)
    {
        return view('schedules.edit', compact('proxySchedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProxyScheduleRequest $request, ProxySchedule $proxySchedule)
    {
        $validated = $request->validated();

        $validated['disable_at'] = Carbon::parse($validated['disable_at']);
        $validated['enable_at']  = Carbon::parse($validated['enable_at']);

        // Si cambia el tipo, recalculamos los site_ids
        if ($validated['type'] === 'laliga_match') {

            $site_ids = ProxySite::where('affected_by_laliga', true)->pluck('id')->toArray();

            if (empty($site_ids)) {
                return redirect()->back()->with('error', 'No hay dominios con LaLiga activado.');
            }

            $validated['site_ids'] = $site_ids;

        } elseif ($validated['type'] === 'ssl_renewal') {
            
            $site_ids = ProxySite::where('ssl_auto_renewal', true)->pluck('id')->toArray();

            if (empty($site_ids)) {
                return redirect()->back()->with('error', 'No hay dominios con renovación SSL automática activada.');
            }

            $validated['site_ids'] = $site_ids;
        }

        $proxySchedule->update($validated);

        return redirect()->route('schedules.index')->with('success', 'Schedule actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProxySchedule $proxySchedule)
    {
        $proxySchedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule eliminado correctamente.');
    }
}

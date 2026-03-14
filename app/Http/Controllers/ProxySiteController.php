<?php

namespace App\Http\Controllers;

use App\Models\ProxySite;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProxySiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sites = ProxySite::all();
        return view('sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('sites.create');
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
    public function show(ProxySite $proxySite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProxySite $proxySite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProxySite $proxySite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProxySite $proxySite)
    {
        //
    }
}

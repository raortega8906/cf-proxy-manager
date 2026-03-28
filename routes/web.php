<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProxyLogController;
use App\Http\Controllers\ProxyScheduleController;
use App\Http\Controllers\ProxySiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::middleware('auth', 'verified')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::patch('/dashboard/deactivate-all', [DashboardController::class, 'deactivateProxyAll'])->name('dashboard.deactivateProxyAll');
    Route::patch('/dashboard/activate-all', [DashboardController::class, 'activateProxyAll'])->name('dashboard.activateProxyAll');
    Route::patch('/dashboard/{site}', [DashboardController::class, 'activateOrDeactivateProxy'])->name('dashboard.activateOrDeactivateProxy');

    // Route::middleware('auth', 'verified')->group(function () {
        // Rutas Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // });

    // Rutas de sitios (CRUD)
    Route::get('/sites', [ProxySiteController::class, 'index'])->name('sites.index');
    Route::get('/sites/create', [ProxySiteController::class, 'create'])->name('sites.create');
    Route::post('/sites', [ProxySiteController::class, 'store'])->name('sites.store');
    Route::get('/sites/{proxySite}/edit', [ProxySiteController::class, 'edit'])->name('sites.edit');
    Route::put('/sites/{proxySite}', [ProxySiteController::class, 'update'])->name('sites.update');
    Route::delete('/sites/{proxySite}', [ProxySiteController::class, 'destroy'])->name('sites.destroy');
    Route::patch('/sites/{site}', [ProxySiteController::class, 'activateOrDeactivateProxy'])->name('sites.activateOrDeactivateProxy');

    // Rutas de schedules (CRUD)
    Route::get('/schedules', [ProxyScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/create', [ProxyScheduleController ::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ProxyScheduleController ::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{proxySchedule}/edit', [ProxyScheduleController ::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{proxySchedule}', [ProxyScheduleController ::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{proxySchedule}', [ProxyScheduleController ::class, 'destroy'])->name('schedules.destroy');

    // Rutas de logs (CRUD)
    Route::get('/logs/export', [ProxyLogController::class, 'export'])->name('logs.export');
    Route::get('/logs', [ProxyLogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->defaults('section', 'dashboard')->name('dashboard');
    Route::get('/database', [AdminDashboardController::class, 'index'])->defaults('section', 'database')->name('database');
    Route::get('/route', [AdminDashboardController::class, 'index'])->defaults('section', 'route')->name('route');
    Route::get('/tracking', [AdminDashboardController::class, 'index'])->defaults('section', 'tracking')->name('tracking');
    Route::get('/requests', [AdminDashboardController::class, 'index'])->defaults('section', 'requests')->name('requests');
    Route::get('/settings', [AdminDashboardController::class, 'index'])->defaults('section', 'settings')->name('settings');
    Route::post('/buses', [AdminDashboardController::class, 'storeBus'])->name('buses.store');
    Route::post('/drivers', [AdminDashboardController::class, 'storeDriver'])->name('drivers.store');
    Route::post('/students', [AdminDashboardController::class, 'storeStudent'])->name('students.store');
    Route::post('/routes', [AdminDashboardController::class, 'storeRoute'])->name('routes.store');
    Route::post('/stops', [AdminDashboardController::class, 'storeStop'])->name('stops.store');
    Route::post('/assignments', [AdminDashboardController::class, 'assignStudent'])->name('assignments.store');
    Route::post('/trips', [AdminDashboardController::class, 'storeTrip'])->name('trips.store');
    Route::patch('/trips/{trip}/complete', [AdminDashboardController::class, 'completeTrip'])->name('trips.complete');
});

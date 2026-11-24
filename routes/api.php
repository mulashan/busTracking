<?php

use App\Http\Controllers\Api\BusLocationController;
use App\Http\Controllers\Api\GpsLocationController;
use Illuminate\Support\Facades\Route;

Route::post('/devices/{deviceId}/locations', [GpsLocationController::class, 'store']);

Route::get('/buses/{bus}/location', [BusLocationController::class, 'show']);
Route::get('/buses/{bus}/history', [BusLocationController::class, 'history']);

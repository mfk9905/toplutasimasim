<?php

use App\Http\Controllers\TransitMapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TransitMapController::class, 'index'])->name('transit.map');
Route::get('/api/live-map/bootstrap', [TransitMapController::class, 'bootstrap'])->name('transit.map.bootstrap');
Route::get('/api/live-map/layers', [TransitMapController::class, 'layers'])->name('transit.map.layers');

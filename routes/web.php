<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/maps', [MapsController::class, 'index'])->name('maps');
Route::get('/maps/polygon', [MapsController::class, 'polygon'])->name('maps.polygon');
Route::get('/maps/line',    [MapsController::class, 'line'])->name('maps.line');       // ← tambah ini

// API internal untuk data Leaflet
Route::get('/api/tempat', [MapsController::class, 'apiTempat'])->name('api.tempat');
Route::get('/api/kecamatan/polygon', [MapsController::class, 'apiKecamatanPolygon'])->name('api.kecamatan.polygon');
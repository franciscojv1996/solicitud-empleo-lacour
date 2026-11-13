<?php

use App\Http\Controllers\SolicitudEmpleoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SolicitudEmpleoController::class, 'index'])
    ->name('solicitud.index');

Route::post('/solicitud-empleo', [SolicitudEmpleoController::class, 'store'])
    ->name('solicitud.store');
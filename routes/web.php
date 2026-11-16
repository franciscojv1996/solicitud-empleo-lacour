<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelSolicitudesController;
use App\Http\Controllers\SolicitudEmpleoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas (Formulario de Postulación de Empleo)
Route::get('/', [SolicitudEmpleoController::class, 'index'])
    ->name('solicitud.index');

Route::post('/solicitud-empleo', [SolicitudEmpleoController::class, 'store'])
    ->name('solicitud.store');

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Rutas Protegidas del Panel de Control (Requieren Inicio de Sesión)
Route::middleware('auth')->group(function () {
    // Panel de Solicitudes
    Route::get('/panel', [PanelSolicitudesController::class, 'index'])
        ->name('panel.index');

    // Inhabilitar / Habilitar Solicitud (Nivel Admin y Maestro)
    Route::patch('/panel/solicitud/{solicitante}/toggle-activo', [PanelSolicitudesController::class, 'toggleActivo'])
        ->name('panel.solicitud.toggle');

    // Eliminación Permanente de Solicitud (Exclusivo Nivel Maestro)
    Route::delete('/panel/solicitud/{solicitante}', [PanelSolicitudesController::class, 'destroy'])
        ->name('panel.solicitud.destroy');

    // Gestión de Usuarios (Nivel Maestro y Admin)
    Route::get('/panel/usuarios', [UsuarioController::class, 'index'])
        ->name('usuarios.index');

    Route::post('/panel/usuarios', [UsuarioController::class, 'store'])
        ->name('usuarios.store');

    Route::delete('/panel/usuarios/{usuario}', [UsuarioController::class, 'destroy'])
        ->name('usuarios.destroy');
});

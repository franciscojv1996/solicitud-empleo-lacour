<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: SessionTimeout
 *
 * Implementa un token de actividad en sesión para detectar inactividad del usuario.
 * Si el usuario lleva más minutos inactivo que SESSION_TIMEOUT_MINUTES (env),
 * su sesión es invalidada y es redirigido al login con un mensaje informativo.
 *
 * Configuración en .env:
 *   SESSION_TIMEOUT_MINUTES=30  → minutos de inactividad permitidos (default: 30)
 *
 * Flujo:
 *  1. Si el usuario NO está autenticado → deja pasar (el middleware 'auth' lo maneja).
 *  2. Si existe la marca '_last_activity' en sesión:
 *     a. Calcula el tiempo transcurrido desde la última actividad.
 *     b. Si supera el límite → cierra sesión y redirige al login.
 *  3. Actualiza '_last_activity' con el timestamp actual.
 */
class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplica a usuarios autenticados
        if (! Auth::check()) {
            return $next($request);
        }

        // Tiempo máximo de inactividad en segundos (default: 30 minutos)
        $timeoutMinutes = (int) env('SESSION_TIMEOUT_MINUTES', 30);
        $timeoutSeconds = $timeoutMinutes * 60;

        $lastActivity = $request->session()->get('_last_activity');

        if ($lastActivity !== null && (time() - $lastActivity) > $timeoutSeconds) {
            // La sesión expiró por inactividad → cerrar sesión limpiamente
            $userName = Auth::user()->name;
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => "La sesión de \"{$userName}\" expiró por inactividad ({$timeoutMinutes} minutos). Por favor, inicia sesión nuevamente.",
            ]);
        }

        // Actualizar la marca de última actividad en cada request
        $request->session()->put('_last_activity', time());

        return $next($request);
    }
}

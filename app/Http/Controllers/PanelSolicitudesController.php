<?php

namespace App\Http\Controllers;

use App\Models\Solicitante;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PanelSolicitudesController extends Controller
{
    /**
     * Muestra el panel de control con el listado de solicitudes recibidas.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();

        $query = Solicitante::with([
            'familiares',
            'educacion',
            'experienciaLaboral',
            'referenciasPersonales',
            'documentos',
        ]);

        // REGLA DE SEGURIDAD POR ROL:
        // - Usuario Estándar: NUNCA ve registros inhabilitados (activo = true).
        // - Admin y Maestro: Pueden ver todos o filtrar entre activos e inhabilitados.
        if ($currentUser->isEstandar()) {
            $query->where('activo', true);
        } else {
            if ($request->input('estado') === 'activos') {
                $query->where('activo', true);
            } elseif ($request->input('estado') === 'inhabilitados') {
                $query->where('activo', false);
            }
        }

        // Filtro de búsqueda por texto (nombre, cédula, correo, teléfono, cargo)
        if ($search = $request->input('buscar')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                    ->orWhere('cedula_numero', 'like', "%{$search}%")
                    ->orWhere('correo', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('cargo_aspira', 'like', "%{$search}%");
            });
        }

        // Filtro por cargo específico
        if ($cargo = $request->input('cargo')) {
            $query->where('cargo_aspira', $cargo);
        }

        // Filtro por posesión de CV
        if ($request->input('con_cv') === 'si') {
            $query->whereHas('documentos', fn ($q) => $q->whereNotNull('cv_ruta'));
        } elseif ($request->input('con_cv') === 'no') {
            $query->whereDoesntHave('documentos', fn ($q) => $q->whereNotNull('cv_ruta'));
        }

        $solicitudes = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Lista de cargos únicos para el filtro desplegable
        $listaCargos = Solicitante::select('cargo_aspira')
            ->distinct()
            ->orderBy('cargo_aspira')
            ->pluck('cargo_aspira');

        return view('panel.index', compact(
            'solicitudes',
            'listaCargos',
            'currentUser'
        ));
    }

    /**
     * Inhabilita o habilita una solicitud (Nivel Admin y Maestro).
     */
    public function toggleActivo(Solicitante $solicitante): RedirectResponse
    {
        $currentUser = Auth::user();

        if (! $currentUser->canDisableSolicitud()) {
            abort(403, 'No tienes permisos para inhabilitar o habilitar registros.');
        }

        $solicitante->activo = ! $solicitante->activo;
        $solicitante->save();

        $estadoTxt = $solicitante->activo ? 'habilitada' : 'inhabilitada';

        return back()->with('status', "La solicitud de {$solicitante->nombres} ha sido {$estadoTxt} correctamente.");
    }

    /**
     * Eliminación permanente de una solicitud (Exclusivo Nivel Maestro / Desarrollador).
     */
    public function destroy(Solicitante $solicitante): RedirectResponse
    {
        $currentUser = Auth::user();

        if (! $currentUser->canDeleteSolicitud()) {
            abort(403, 'Acceso denegado: solo el usuario Maestro (Desarrollador) puede eliminar registros.');
        }

        // Si tiene documentos adjuntos, eliminar los archivos físicos del almacenamiento
        if ($solicitante->documentos) {
            $doc = $solicitante->documentos;
            if ($doc->cv_ruta) {
                Storage::disk('public')->delete($doc->cv_ruta);
            }
            if ($doc->cedula_ruta) {
                Storage::disk('public')->delete($doc->cedula_ruta);
            }
            if ($doc->rif_ruta) {
                Storage::disk('public')->delete($doc->rif_ruta);
            }
            $doc->delete();
        }

        // Eliminar registros hijos en cascada
        $solicitante->familiares()->delete();
        $solicitante->educacion()->delete();
        $solicitante->experienciaLaboral()->delete();
        $solicitante->referenciasPersonales()->delete();

        $nombre = $solicitante->nombres;
        $solicitante->delete();

        return back()->with('status', "La solicitud de {$nombre} ha sido eliminada permanentemente del sistema.");
    }
}

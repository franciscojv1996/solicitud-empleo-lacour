<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Muestra la lista de usuarios y el formulario para crear uno nuevo.
     */
    public function index(): View|RedirectResponse
    {
        $currentUser = Auth::user();

        // Nivel Estándar no tiene acceso a gestión de usuarios
        if (! $currentUser->canManageUsers()) {
            abort(403, 'No tienes permisos para gestionar usuarios.');
        }

        $usuarios = User::orderBy('id', 'asc')->get();
        $rolesPermitidos = $currentUser->creatableRoles();

        return view('panel.usuarios', compact('usuarios', 'rolesPermitidos', 'currentUser'));
    }

    /**
     * Registra un nuevo usuario respetando la jerarquía de seguridad.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentUser = Auth::user();

        if (! $currentUser->canManageUsers()) {
            abort(403, 'No tienes permisos para crear usuarios.');
        }

        $rolesPermitidos = array_keys($currentUser->creatableRoles());

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', 'string', Rule::in($rolesPermitidos)],
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role.required' => 'Debes seleccionar un nivel de seguridad.',
            'role.in' => 'No tienes autorización para asignar ese nivel de seguridad.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('usuarios.index')->with('success', "Usuario {$validated['name']} creado exitosamente.");
    }

    /**
     * Elimina un usuario según jerarquía.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        $currentUser = Auth::user();

        // No permitir que un usuario se elimine a sí mismo
        if ($currentUser->id === $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta de usuario.');
        }

        // Maestro puede eliminar a cualquiera
        if ($currentUser->isMaestro()) {
            $usuario->delete();

            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        }

        // Admin solo puede eliminar usuarios de nivel estándar
        if ($currentUser->isAdmin() && $usuario->isEstandar()) {
            $usuario->delete();

            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        }

        abort(403, 'No tienes privilegios suficientes para eliminar este usuario.');
    }
}

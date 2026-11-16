<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Nivel Maestro (Desarrollador): Eliminación definitiva y gestión total de usuarios.
     */
    public function isMaestro(): bool
    {
        return $this->role === 'maestro';
    }

    /**
     * Nivel Admin (Gerente): Inhabilitar registros y crear usuarios de nivel igual o inferior.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Nivel Estándar: Solo lectura, no crea usuarios ni inhabilita registros.
     */
    public function isEstandar(): bool
    {
        return $this->role === 'estandar' || empty($this->role);
    }

    /**
     * Puede eliminar permanentemente solicitudes (Solo Maestro).
     */
    public function canDeleteSolicitud(): bool
    {
        return $this->isMaestro();
    }

    /**
     * Puede inhabilitar / habilitar solicitudes (Maestro y Admin).
     */
    public function canDisableSolicitud(): bool
    {
        return $this->isMaestro() || $this->isAdmin();
    }

    /**
     * Puede administrar y crear usuarios (Maestro y Admin).
     */
    public function canManageUsers(): bool
    {
        return $this->isMaestro() || $this->isAdmin();
    }

    /**
     * Retorna los roles que este usuario tiene permiso de crear.
     */
    public function creatableRoles(): array
    {
        if ($this->isMaestro()) {
            return [
                'maestro' => 'Maestro (Desarrollador)',
                'admin' => 'Administrador (Gerente)',
                'estandar' => 'Estándar (Solo Lectura)',
            ];
        }

        if ($this->isAdmin()) {
            return [
                'admin' => 'Administrador (Gerente)',
                'estandar' => 'Estándar (Solo Lectura)',
            ];
        }

        return [];
    }

    /**
     * Etiqueta legible del rol.
     */
    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'maestro' => 'Maestro (Desarrollador)',
            'admin' => 'Administrador (Gerente)',
            default => 'Estándar (Visualizador)',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

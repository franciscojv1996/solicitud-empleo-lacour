<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Se ejecuta una vez al arrancar el proyecto.
     * Llama a la función de verificación/creación del usuario Maestro.
     */
    public function boot(): void
    {
        $this->garantizarUsuarioMaestro();
    }

    /**
     * Garantiza que exista al menos un usuario con rol "maestro" en el sistema.
     *
     * Lógica:
     *  - Consulta la tabla `users` buscando un registro con role = 'maestro'.
     *  - Si NO existe: lo crea con los datos de las variables de entorno
     *    MASTER_USER_NAME, MASTER_USER_EMAIL y MASTER_USER_PASSWORD.
     *  - Si SÍ existe: registra en el log que el usuario Maestro está activo.
     *
     * Variables de entorno requeridas (.env):
     *   MASTER_USER_NAME     → nombre del usuario maestro
     *   MASTER_USER_EMAIL    → correo del usuario maestro
     *   MASTER_USER_PASSWORD → contraseña en texto plano (se hashea al crear)
     *
     * Nota: Se verifica que la tabla `users` exista antes de consultar para
     * evitar errores durante la primera migración o en entornos limpios.
     */
    protected function garantizarUsuarioMaestro(): void
    {
        // Protección: si la tabla users no existe aún (ej: antes de migrate), no hacer nada
        if (! Schema::hasTable('users')) {
            Log::warning('[MaestroInit] La tabla "users" no existe. Saltando verificación del usuario Maestro.');

            return;
        }

        $maestroExistente = User::where('role', 'maestro')->first();

        if ($maestroExistente === null) {
            // No existe ningún usuario Maestro → crearlo desde las variables de entorno
            $nombre = env('MASTER_USER_NAME', 'Master');
            $email = env('MASTER_USER_EMAIL', 'master@grupolacour.com');
            $password = env('MASTER_USER_PASSWORD', 'master123');

            if (! preg_match('/^[^@\s]+@grupolacour\.com$/i', $email)) {
                Log::error('[MaestroInit] MASTER_USER_EMAIL debe terminar en @grupolacour.com. No se creó el usuario Maestro.');

                return;
            }

            User::create([
                'name' => $nombre,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'maestro',
            ]);

            Log::info("[MaestroInit] ✅ Usuario Maestro creado exitosamente: {$email}");
        } else {
            // Ya existe un usuario Maestro → solo notificar en el log
            Log::info("[MaestroInit] 👑 Usuario Maestro activo: {$maestroExistente->name} ({$maestroExistente->email})");
        }
    }
}

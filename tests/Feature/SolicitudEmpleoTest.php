<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SolicitudEmpleoTest extends TestCase
{
    /**
     * Verifica que la página principal con el formulario cargue correctamente.
     */
    public function test_formulario_solicitud_carga_correctamente(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SOLICITUD DE EMPLEO');
        $response->assertSee('data-url', false);
        $response->assertSee('novalidate', false);
    }

    /**
     * Verifica que el endpoint de guardado valide campos obligatorios y devuelva JSON 422.
     */
    public function test_validacion_campos_requeridos_devuelve_errores_json(): void
    {
        $response = $this->postJson('/solicitud-empleo', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'cargo_aspira',
            'fecha_solicitud',
            'nombres',
            'cedula_tipo',
            'cedula_numero',
            'telefono',
            'correo',
            'acceptTerms',
        ]);
        $response->assertJsonFragment([
            'cargo_aspira' => ['El cargo al que aspira es obligatorio.'],
        ]);
    }

    /**
     * Verifica que un visitante no autenticado sea redirigido al login al intentar entrar al panel.
     */
    public function test_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        $response = $this->get('/panel');

        $response->assertRedirect('/login');
    }

    /**
     * Verifica que la pantalla de login cargue correctamente.
     */
    public function test_login_carga_correctamente(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Acceso al Panel de Control');
    }

    /**
     * Verifica que el usuario Maestro tenga acceso total (inhabilitar, eliminar y gestión de usuarios).
     */
    public function test_usuario_maestro_tiene_acceso_total_incluyendo_eliminacion(): void
    {
        $maestro = User::where('role', 'maestro')->first() ?? User::factory()->create(['role' => 'maestro']);

        $response = $this->actingAs($maestro)->get('/panel');

        $response->assertStatus(200);
        $response->assertSee('Panel Administrativo de Solicitudes de Empleo');
        $response->assertSee('Gestión de Usuarios');
        $response->assertSee('Eliminar');
        $response->assertSee('Inhabilitar');
    }

    /**
     * Verifica que el usuario Admin pueda inhabilitar pero no tenga botón de eliminar.
     */
    public function test_usuario_admin_puede_inhabilitar_pero_no_eliminar(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/panel');

        $response->assertStatus(200);
        $response->assertSee('Inhabilitar');
        $response->assertDontSee('🗑️ Eliminar');
    }

    /**
     * Verifica que el usuario Estándar sea solo lectura (sin eliminar, sin inhabilitar y sin usuarios).
     */
    public function test_usuario_estandar_es_solo_lectura(): void
    {
        $estandar = User::where('role', 'estandar')->first() ?? User::factory()->create(['role' => 'estandar']);

        $response = $this->actingAs($estandar)->get('/panel');

        $response->assertStatus(200);
        $response->assertDontSee('🗑️ Eliminar');
        $response->assertDontSee('🚫 Inhabilitar');
        $response->assertDontSee('Gestión de Usuarios');
    }

    /**
     * Verifica que un Admin no pueda crear un usuario con nivel Maestro.
     */
    public function test_admin_no_puede_crear_usuario_maestro(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/panel/usuarios', [
            'name' => 'Intento Maestro',
            'email' => 'intento_maestro@lacour.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'maestro',
        ]);

        $response->assertSessionHasErrors('role');
    }
}

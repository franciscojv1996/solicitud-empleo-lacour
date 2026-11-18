<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Nivel Maestro (Desarrollador)
        User::updateOrCreate(
            ['email' => 'maestro@grupolacour.com'],
            [
                'name' => 'Desarrollador Maestro',
                'password' => Hash::make('maestro123'),
                'role' => 'maestro',
            ]
        );

        // 2. Nivel Admin (Gerente)
        User::updateOrCreate(
            ['email' => 'admin@grupolacour.com'],
            [
                'name' => 'Gerente de Operaciones',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 3. Nivel Estándar (Visualizador)
        User::updateOrCreate(
            ['email' => 'estandar@grupolacour.com'],
            [
                'name' => 'Analista de Recursos Humanos',
                'password' => Hash::make('estandar123'),
                'role' => 'estandar',
            ]
        );
    }
}

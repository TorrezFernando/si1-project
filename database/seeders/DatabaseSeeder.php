<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Seeder principal: crea datos minimos para entrar al sistema.
class DatabaseSeeder extends Seeder
{
    // Desactiva eventos de modelos durante la carga inicial.
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // CU01: Crea o actualiza el usuario administrador inicial.
        User::updateOrCreate(
            ['id_user' => 1],
            [
                'username' => 'admin',
                'password' => Hash::make('password'),
                'id_rol' => 1,
            ]
        );

        $this->call(TestDataSeeder::class);
    }
}

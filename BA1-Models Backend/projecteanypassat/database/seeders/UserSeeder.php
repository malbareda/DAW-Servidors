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
        // Crear usuari administrador predefinit
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@biblioteca.cat',
            'password' => Hash::make('password123'),
        ]);

        // Crear usuaris amb dades generades per Faker
        User::factory(20)->create();
    }
}

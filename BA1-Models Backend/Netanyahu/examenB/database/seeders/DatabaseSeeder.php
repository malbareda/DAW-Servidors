<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //los seeders se llaman siempre asi. Te lo vas copiando entre proyectos.
        $this->call([
            LlibreSeeder::class,
            BibliotecariSeeder::class,
        ]);

        //USUARIO PARA PODER HACER LOGIN (la contraseña es 'password', mirad el UserFactory)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

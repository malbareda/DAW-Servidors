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
        // User::factory(10)->create();
        //los seeders se llaman siempre asi. Te lo vas copiando entre proyectos.
        //EL ORDEN IMPORTA: primero artistas, despues conciertos (que necesitan artista_id)
        $this->call([
            ArtistaSeeder::class,
            ConcertSeeder::class,
        ]);

        //USUARIO PARA PODER HACER LOGIN (la contraseña es 'password', mirad el UserFactory)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

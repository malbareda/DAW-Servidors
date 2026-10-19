<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artista;

class ArtistaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //INSERT DE GOLPE, como hay que poner created_at y updated_at a mano
        //(insert no los pone solo, create si)
        Artista::insert([
            [
                'nom' => 'Rosalía',
                'pais' => 'Espanya',
                'bio' => 'Cantant i compositora catalana.',
                'imatge_url' => null,
                'actiu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Bad Gyal',
                'pais' => 'Espanya',
                'bio' => 'Reina del dancehall de Vilassar de Mar.',
                'imatge_url' => null,
                'actiu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Daft Punk',
                'pais' => 'França',
                'bio' => 'Duo de música electrònica amb casc.',
                'imatge_url' => null,
                'actiu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Bad Bunny',
                'pais' => 'Puerto Rico',
                'bio' => 'El conill dolent del reggaeton.',
                'imatge_url' => null,
                'actiu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Billie Eilish',
                'pais' => 'Estats Units',
                'bio' => 'Cantant que xiuxiueja al micro.',
                'imatge_url' => null,
                'actiu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Stromae',
                'pais' => 'Bèlgica',
                'bio' => 'Cantant i productor de Brussel·les.',
                'imatge_url' => null,
                'actiu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            //ESTOS DOS NO ESTAN CONFIRMADOS (actiu = false)
            [
                'nom' => 'Els Pets',
                'pais' => 'Espanya',
                'bio' => 'Grup de rock de Constantí.',
                'imatge_url' => null,
                'actiu' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Taylor Swift',
                'pais' => 'Estats Units',
                'bio' => 'Massa cara per al festival.',
                'imatge_url' => null,
                'actiu' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

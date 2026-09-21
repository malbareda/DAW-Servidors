<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ciudad;

class CiudadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Ciudad::insert([
            [
            'nombre' => 'Sabadell',
            'habitantes' => 250000,
            'pais' => 'Espanya',
            'superficie' => 2,
            'alcalde' => 'Marte Farrés',
            'amigo' => false,
            'estado' => 'colonizable',
            'created_at' => now(),
            'updated_at' => now(),
            ],

            [
                'nombre' => 'Tel Aviv',
                'habitantes' => 1000000,
                'pais' => 'Israel',
                'superficie' => 5,
                'alcalde' => 'Netanyahu',
                'amic' => true,
                'estado' => 'bombardeable',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}

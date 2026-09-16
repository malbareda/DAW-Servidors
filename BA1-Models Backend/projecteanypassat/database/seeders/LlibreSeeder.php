<?php

namespace Database\Seeders;

use App\Models\Llibre;
use Illuminate\Database\Seeder;

class LlibreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 15 llibres de ficció amb la factory
        Llibre::factory(15)->ficcio()->create();

        // Crear 15 llibres de no ficció amb la factory
        Llibre::factory(15)->noFiccio()->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Poni;
use Illuminate\Database\Seeder;

class PoniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //INSERT DE GOLPE, com cal posar created_at i updated_at a ma
        //(insert no els posa sol, create si). Sense user_id: son de l'hipica.
        Poni::insert([
            ['nom' => 'Estrella', 'edat' => 5, 'mida' => 'MITJA', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Pinxo', 'edat' => 2, 'mida' => 'PETIT', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

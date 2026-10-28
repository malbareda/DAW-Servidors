<?php

namespace Database\Seeders;

use App\Models\Ejercito;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ciudad;

class EjercitoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Ejercito::insert([
            ['nombre' => 'Mossad', 'soldados' => 5000, 'ciudad_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Talibanes', 'soldados' => 3000, 'ciudad_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'ISIS', 'soldados' => 2000, 'ciudad_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'ETA', 'soldados' => 1500, 'ciudad_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Al-Qaeda', 'soldados' => 20, 'ciudad_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

}

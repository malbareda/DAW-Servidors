<?php

namespace Database\Seeders;

use App\Models\Llibre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LlibreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //INSERT DE GOLPE, como hay que poner created_at y updated_at a mano
        //(insert no los pone solo, create si)
        Llibre::insert([
            ['titol' => 'Mecanoscrit del segon origen', 'categoria' => 'FICCIO', 'created_at' => now(), 'updated_at' => now()],
            ['titol' => 'Tirant lo Blanc', 'categoria' => 'FICCIO', 'created_at' => now(), 'updated_at' => now()],
            ['titol' => 'Sapiens', 'categoria' => 'NOFICCIO', 'created_at' => now(), 'updated_at' => now()],
            ['titol' => 'Història de Catalunya', 'categoria' => 'NOFICCIO', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

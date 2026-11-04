<?php

namespace Database\Seeders;

use App\Models\Bibliotecari;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BibliotecariSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //EL EXAMEN DICE DE PONER UN BIBLIOTECARI A MANO EN LA BD
        //con el seeder ya queda puesto cada vez que hago migrate:fresh --seed
        Bibliotecari::insert([
            ['nom' => 'Anna', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Jordi', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

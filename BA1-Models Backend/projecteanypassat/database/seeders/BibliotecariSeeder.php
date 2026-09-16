<?php

namespace Database\Seeders;

use App\Models\Bibliotecari;
use Illuminate\Database\Seeder;

class BibliotecariSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear bibliotecaris amb dades generades completament per Faker
        Bibliotecari::factory(15)->create();
    }
}

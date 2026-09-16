<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,               // Primers els usuaris
            BibliotecariSeeder::class,       // Després els bibliotecaris
            LlibreSeeder::class,             // Després els llibres
            BibliotecariLlibreSeeder::class, // Després les relacions entre bibliotecaris i llibres
            PrestecSeeder::class,            // Finalment els préstecs
        ]);
    }
}

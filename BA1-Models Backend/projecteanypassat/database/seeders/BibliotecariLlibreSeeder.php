<?php

namespace Database\Seeders;

use App\Models\Bibliotecari;
use App\Models\Llibre;
use Illuminate\Database\Seeder;

class BibliotecariLlibreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenir tots els bibliotecaris i llibres
        $bibliotecaris = Bibliotecari::all();
        $llibres = Llibre::all();

        if ($bibliotecaris->count() === 0 || $llibres->count() === 0) {
            $this->command->info('No hi ha bibliotecaris o llibres disponibles per crear relacions!');
            return;
        }

        // Per cada bibliotecari, assignar entre 3 i 8 llibres aleatoris
        foreach ($bibliotecaris as $bibliotecari) {
            $numLlibres = rand(3, min(8, $llibres->count()));
            $llibresAssignats = $llibres->random($numLlibres);

            $bibliotecari->llibres()->attach($llibresAssignats->pluck('id')->toArray());
        }

        // Assegurar que tots els llibres tenen almenys un bibliotecari assignat
        foreach ($llibres as $llibre) {
            if ($llibre->bibliotecaris()->count() === 0) {
                $bibliotecari = $bibliotecaris->random();
                $llibre->bibliotecaris()->attach($bibliotecari->id);
            }
        }
    }
}

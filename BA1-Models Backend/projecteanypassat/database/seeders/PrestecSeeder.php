<?php

namespace Database\Seeders;

use App\Models\Prestec;
use App\Models\Llibre;
use App\Models\User;
use Illuminate\Database\Seeder;

class PrestecSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenir tots els llibres i usuaris per fer-los servir als préstecs
        $llibres = Llibre::all();
        $users = User::all();

        if ($llibres->count() === 0 || $users->count() === 0) {
            $this->command->info('No hi ha llibres o usuaris disponibles per crear préstecs!');
            return;
        }
        Prestec::factory()->count(35)->create();
        /*
        // Crear préstecs actius (15)
        foreach ($llibres->random(min(15, $llibres->count())) as $llibre) {
            Prestec::factory()->actiu()->create([
                'llibre_id' => $llibre->id,
                'user_id' => $users->random()->id,
            ]);
        }

        // Crear préstecs retornats (25)
        for ($i = 0; $i < min(25, $llibres->count()); $i++) {
            Prestec::factory()->retornat()->create([
                'llibre_id' => $llibres->random()->id,
                'user_id' => $users->random()->id,
            ]);
        }
        */
    }
}

<?php

namespace Database\Seeders;

use App\Models\Concert;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConcertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //LOS artista_id SON LOS DEL ArtistaSeeder, POR ESO ESTE VA DESPUES
        //1 Rosalía, 2 Bad Gyal, 3 Daft Punk, 4 Bad Bunny, 5 Billie Eilish, 6 Stromae
        //el 7 y el 8 estan inactivos, NO les pongo conciertos
        //fechas en verano (el festival es en julio)
        Concert::insert([
            ['artista_id' => 1, 'nom' => 'Motomami World Tour', 'data_hora' => '2027-07-08 22:00:00', 'durada_minuts' => 90, 'tipus' => 'CONCERT', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 1, 'nom' => 'Rosalía Íntima', 'data_hora' => '2027-07-10 19:00:00', 'durada_minuts' => 45, 'tipus' => 'ACUSTIC', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 1, 'nom' => 'Nou disc en primícia', 'data_hora' => '2027-07-09 17:00:00', 'durada_minuts' => 30, 'tipus' => 'SHOWCASE', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 2, 'nom' => 'La Joia Tour', 'data_hora' => '2027-07-08 23:59:00', 'durada_minuts' => 75, 'tipus' => 'CONCERT', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 2, 'nom' => 'Bad Gyal Sound System', 'data_hora' => '2027-07-11 02:00:00', 'durada_minuts' => 120, 'tipus' => 'DJ_SET', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 3, 'nom' => 'Alive 2027', 'data_hora' => '2027-07-09 23:30:00', 'durada_minuts' => 150, 'tipus' => 'DJ_SET', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 3, 'nom' => 'Random Access Memories', 'data_hora' => '2027-07-10 22:00:00', 'durada_minuts' => 100, 'tipus' => 'CONCERT', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 3, 'nom' => 'Daft Punk B2B', 'data_hora' => '2027-07-11 04:00:00', 'durada_minuts' => 180, 'tipus' => 'DJ_SET', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 4, 'nom' => 'Most Wanted Tour', 'data_hora' => '2027-07-10 23:00:00', 'durada_minuts' => 110, 'tipus' => 'CONCERT', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 4, 'nom' => 'Bad Bunny Unplugged', 'data_hora' => '2027-07-11 18:00:00', 'durada_minuts' => 50, 'tipus' => 'ACUSTIC', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 5, 'nom' => 'Hit Me Hard and Soft', 'data_hora' => '2027-07-09 21:00:00', 'durada_minuts' => 85, 'tipus' => 'CONCERT', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 5, 'nom' => 'Billie & Finneas', 'data_hora' => '2027-07-10 18:30:00', 'durada_minuts' => 40, 'tipus' => 'ACUSTIC', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 5, 'nom' => 'Presentació nou single', 'data_hora' => '2027-07-08 17:30:00', 'durada_minuts' => 20, 'tipus' => 'SHOWCASE', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 6, 'nom' => 'Multitude Tour', 'data_hora' => '2027-07-11 22:30:00', 'durada_minuts' => 95, 'tipus' => 'CONCERT', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 6, 'nom' => 'Stromae Showcase', 'data_hora' => '2027-07-09 16:00:00', 'durada_minuts' => 25, 'tipus' => 'SHOWCASE', 'created_at' => now(), 'updated_at' => now()],
            ['artista_id' => 6, 'nom' => 'Papaoutai Acústic', 'data_hora' => '2027-07-10 16:30:00', 'durada_minuts' => 35, 'tipus' => 'ACUSTIC', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

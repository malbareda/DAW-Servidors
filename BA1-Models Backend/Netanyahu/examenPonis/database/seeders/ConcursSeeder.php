<?php

namespace Database\Seeders;

use App\Models\Concurs;
use Illuminate\Database\Seeder;

class ConcursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Concurs::insert([
            ['nom' => 'Concurs de Primavera', 'data' => '2026-04-12', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Copa de Nadal', 'data' => '2026-12-20', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

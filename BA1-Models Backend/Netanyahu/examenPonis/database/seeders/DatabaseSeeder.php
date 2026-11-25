<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //els seeders es criden sempre aixi. Users no en necessita: t'hi registres.
        $this->call([
            PoniSeeder::class,
            ConcursSeeder::class,
        ]);
    }
}

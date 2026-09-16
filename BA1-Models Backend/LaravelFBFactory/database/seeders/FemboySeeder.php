<?php

namespace Database\Seeders;

use App\Models\Femboy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FemboySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \Database\Factories\FemboyFactory::new()->count(20)->joven()->create();


        \Database\Factories\FemboyFactory::new()->count(20)->experimentado()->create();

    }
}

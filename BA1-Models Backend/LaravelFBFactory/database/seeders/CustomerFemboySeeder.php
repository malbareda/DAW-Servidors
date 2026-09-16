<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Femboy;
use Illuminate\Database\Seeder;

class CustomerFemboySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $femboys = Femboy::all();

        //aixo es per debugars
        $this->command->info($customers->count());


        if ($customers->count() === 0 || $femboys->count() === 0) {
            //aixo es per debugar en vermell
            $this->command->error('No hi ha customers o femboys disponibles!');
            return;
        }

        $this->command->info('Creant relacions Customer-Femboy...');

        // Per cada customer, assignar entre 2 i 5 femboys
        foreach ($customers as $customer) {
            $numFemboys = rand(2, 5);
            $femboysAssignats = $femboys->random($numFemboys);
            foreach ($femboysAssignats as $femboysAssignat) {
                $customer->femboys()->attach($femboysAssignat);
            }
        }

    }
}

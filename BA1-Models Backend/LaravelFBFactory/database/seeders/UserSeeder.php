<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // USUARIS CREATS MANUALMENT
        // ========================================

        // Admin principal
        User::create([
            'name' => 'admin',
            'email' => 'marc@joelestudis.com',
            'password' => Hash::make('password123'),
            'femboy_enjoyer' => true,
            'email_verified_at' => now(),
        ]);

        // Admin secundari
        User::create([
            'name' => 'Edgar Lara',
            'email' => 'edgar@joelestudis.com',
            'password' => Hash::make('password123'),
            'femboy_enjoyer' => true,
            'email_verified_at' => now(),
        ]);

        // Usuari de proves
        User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('test1234'),
            'femboy_enjoyer' => false,
            'email_verified_at' => now(),
        ]);

        // Usuari VIP
        User::create([
            'name' => 'Cliente Premium',
            'email' => 'premium@vip.com',
            'password' => Hash::make('vip12345'),
            'femboy_enjoyer' => true,
            'email_verified_at' => now(),
        ]);

        // ========================================
        // USUARIS GENERATS AMB FAKER
        // ========================================

        // 15 usuaris normals amb Faker
        User::factory(15)->create();

        // 5 usuaris sense verificar email
        User::factory(5)->unverified()->create();

        $this->command->info('Usuaris creats: 4 manuals + 20 amb Faker');
    }
}

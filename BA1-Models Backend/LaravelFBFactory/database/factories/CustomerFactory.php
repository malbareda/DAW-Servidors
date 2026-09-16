<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use App\Models\Factory as FactoryModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Locale espanyol per DNIs i noms
        $this->faker = \Faker\Factory::create('es_ES');

        $factory_id = null;
        if($this->faker->boolean(50)){
            $factory_id = \App\Models\Factory::factory();
        }

        return [
            'nom' => $this->faker->name(),
            'dni' => $this->generateDNI(),
            'factory_id' => $factory_id,
            'activo' => $this->faker->boolean(80), // 80% actius
            'user_id' => User::factory(),
        ];
    }

    /**
     * Genera un DNI espanyol vàlid (format correcte)
     */
    private function generateDNI(): string
    {
        $numeros = $this->faker->numerify('########');
        $letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $letra = $letras[$numeros % 23];
        return $numeros . $letra;
    }
}

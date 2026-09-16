<?php

namespace Database\Factories;

use App\Models\Femboy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Femboy>
 */
class FactoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Factory::class;


    public function definition(): array
    {

        $this->faker = \Faker\Factory::create('pt_BR');

        return [
            'nombre' => $this->faker->company(),
            'ubicacion' => $this->faker->address() . ', ' . $this->faker->city(). ', ' . $this->faker->country(),
            'cif' => $this->generateCIF()
        ];
    }

    /**
     * Genera un CIF espanyol vàlid (simulat)
     */
    private function generateCIF(): string
    {
        $letra = $this->faker->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J']);
        $numeros = $this->faker->numerify('########');
        return $letra . $numeros;
    }
}

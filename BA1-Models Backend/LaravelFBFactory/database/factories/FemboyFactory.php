<?php

namespace Database\Factories;

use App\Models\Femboy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Femboy>
 */
class FemboyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Femboy::class;


    public function definition(): array
    {
        $this->faker = \Faker\Factory::create('pt_BR');

        return [
            'nombre' => $this->faker->name(),
            'procedencia' => $this->faker->city(),
            'fecha_cumpleanos'=>$this->faker->dateTimeBetween('-21 years', '-18 years' ),
            'descripcion' => $this->faker->sentence(10, 2),
            'estado' => $this->faker->randomElement(['Disponible', 'Reservado', 'No Disponible']),
            'factory_id' => \App\Models\Factory::inRandomOrder()->first()?->id,
        ];
    }

    /**
     * Femboys joves (18-22 anys)
     */
    public function joven(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'fecha_cumpleanos' => $this->faker->dateTimeBetween('-21 years', '-18 years'),
            ];
        });
    }

    /**
     * Femboys experimentats (25-30 anys)
     */
    public function experimentado(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'fecha_cumpleanos' => $this->faker->dateTimeBetween('-25 years', '-21 years'),
            ];
        });
    }
}

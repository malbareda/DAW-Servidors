<?php

namespace Database\Factories;

use App\Models\Llibre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Llibre>
 */
class LlibreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Llibre::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $this->faker = \Faker\Factory::create('pt_BR');

        return [
            'titol' =>$this->faker->sentence(),
            'categoria' => $this->faker->randomElement(['FICCIO', 'NOFICCIO']),
        ];
    }

    /**
     * Estat per a llibres de ficció.
     */
    public function ficcio(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'categoria' => 'FICCIO',
            ];
        });
    }

    /**
     * Estat per a llibres de no ficció.
     */
    public function noFiccio(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'categoria' => 'NOFICCIO',
            ];
        });
    }
}

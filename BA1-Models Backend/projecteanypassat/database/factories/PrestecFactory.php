<?php

namespace Database\Factories;

use App\Models\Prestec;
use App\Models\Llibre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prestec>
 */
class PrestecFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Prestec::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker = \Faker\Factory::create('es_ES');

        return [
            'actiu' => $this->faker->boolean(70), // 70% de probabilitat que sigui actiu
            'data' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'llibre_id' => Llibre::factory(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Estat per a préstecs actius.
     */
    public function actiu(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'actiu' => true,
                'data' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }

    /**
     * Estat per a préstecs retornats.
     */
    public function retornat(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'actiu' => false,
                'data' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
            ];
        });
    }
}

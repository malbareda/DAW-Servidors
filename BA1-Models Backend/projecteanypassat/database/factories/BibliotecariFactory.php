<?php

namespace Database\Factories;

use App\Models\Bibliotecari;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bibliotecari>
 */
class BibliotecariFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bibliotecari::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker = \Faker\Factory::create('uk_UK');

        return [
            'nom' => $this->faker->name(),
        ];
    }
}

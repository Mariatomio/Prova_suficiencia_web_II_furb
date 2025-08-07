<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ComandaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'idUsuario' => $this->faker->numberBetween(1, 12),
        ];
    }
}

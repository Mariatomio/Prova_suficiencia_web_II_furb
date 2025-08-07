<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ComandaProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'idUsuario' => $this->faker->numberBetween(1, 12),
            'idProduto' => $this->faker->numberBetween(1, 10),
        ];
    }
}

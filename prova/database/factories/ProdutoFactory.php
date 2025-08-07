<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'idUsuario' => $this->faker->numberBetween(1, 11),
            'nome' => $this->faker->name(),
            'preco' => $this->faker->randomFloat(3, 1, 100),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nomeUsuario' => $this->faker->name(),
            'telefoneUsuario' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), 
        ];
    }
}

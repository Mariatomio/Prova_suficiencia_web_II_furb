<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comanda;
use App\Models\ComandaProduto;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       /*  Produto::factory(10)->create();
        Usuario::factory(10)->create();
        Comanda::factory(10)->create();
        ComandaProduto::factory(10)->create(); */

        Usuario::create([
            'nomeUsuario' => 'Maria Teste',
            'telefoneUsuario' => '47999999999',
            'email' => 'teste@example.com',
            'password' => bcrypt('senha123')
        ]);

    }
}

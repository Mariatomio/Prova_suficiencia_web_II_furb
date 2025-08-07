<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comanda_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idComanda'); 
            $table->unsignedBigInteger('idProduto'); 
            $table->timestamps();

            $table->foreign('idComanda')->references('id')->on('comandas');
            $table->foreign('idProduto')->references('id')->on('produtos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comanda_produtos');
    }
};

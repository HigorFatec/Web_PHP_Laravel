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
        Schema::create('transf__veiculos', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('name');
            $table->string('email');
            $table->string('placa');
            $table->string('placa_carreta')->nullable();
            $table->string('placa_carreta_2')->nullable();
            $table->string('placa_carreta_3')->nullable();
            $table->string('filial_origem');
            $table->string('filial_destino');
            $table->string('centro_custo');
            $table->string('centro_gasto');
            $table->date('previsao_chegada');
            $table->string('conferencia_pneus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transf__veiculos');
    }
};

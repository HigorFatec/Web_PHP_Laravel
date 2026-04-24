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
        // Tabela de Setores
        Schema::create('setores', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // ex: 'financeiro', 'fiscal', 'suprimentos'
            $table->timestamps();
        });

        // Tabela Pivô (setor_user)
        Schema::create('setor_user', function (Blueprint $table) {
            $table->id();
            // Aqui explicamos que a referência de 'setor_id' está na tabela 'setores'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('setor_id')->constrained('setores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setor');
    }
};

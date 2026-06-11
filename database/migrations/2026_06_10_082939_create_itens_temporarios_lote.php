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
        Schema::create('itens_temporarios_lote', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Para garantir que um usuário não veja o rascunho do outro
            $table->string('codprod');
            $table->string('descricao');
            $table->string('grupo')->nullable();
            $table->string('subgrupo')->nullable();
            $table->integer('quantidade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_temporarios_lote');
    }
};

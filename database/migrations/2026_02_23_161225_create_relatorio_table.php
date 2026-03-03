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
        Schema::create('relatorio', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('motivo');
            $table->string('cod_unidade');
            $table->string('cod_custo');
            $table->string('cod_gasto');
            $table->string('gestor_aprovador');
            $table->date('inicio_viagem');
            $table->date('fim_viagem');
            
            // Dados do usuário (Snapshot)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_name');
            $table->string('user_email');
            
            // Controle de fluxo
            $table->string('status')->default('pendente'); // pendente, aprovado, rejeitado
            $table->uuid('approval_token')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorio');
    }
};

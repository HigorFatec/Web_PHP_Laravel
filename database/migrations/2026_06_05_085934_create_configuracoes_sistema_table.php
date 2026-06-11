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
        Schema::create('configuracoes_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('chave')->unique(); // ex: 'status_pix_fr'
            // tinyInteger com default 0 para economizar espaço e indexar rápido
            $table->tinyInteger('valor')->default(0); 
            $table->string('descricao')->nullable();
            $table->timestamps();
        });

        // Já insere a linha que o seu switch vai ler e alterar
        DB::table('configuracoes_sistema')->insert([
            'chave' => 'status_pix_fr',
            'valor' => 0, // Começa desativado (OFF)
            'descricao' => 'Controla a ativação do módulo Pix Flow F&R Real Time',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracoes_sistema');
    }
};

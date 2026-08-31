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
        // Como o padrão do Laravel é o MySQL, usa a conexão padrão
        Schema::create('historico_importacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID do usuário logado
            $table->string('nome_arquivo');
            $table->integer('total_registros');
            $table->timestamps(); // Cria 'created_at' e 'updated_at'

            // Opcional: Se você tiver a tabela padrão de usuários do Laravel, pode criar a chave estrangeira
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_importacoes');
    }
};

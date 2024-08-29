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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('ok');
            $table->string('origem');
            $table->string('destino');
            $table->enum('tipo',['aerea','rodoviaria']);
            $table->date('ida');
            $table->date('volta')->nullable();
            $table->string('motivo');
            $table->string('validacao');
            $table->string('email_gestor');
            $table->string('observacoes')->nullable();
            $table->string('nome');
            $table->string('cpf');
            $table->string('rg');
            $table->date('data_nascimento');
            $table->string('email');

            // Colunas de chave estrangeira
            $table->string('user_name');
            $table->string('user_cpf');
            $table->string('user_email');

            // Definição das chaves estrangeiras
            $table->foreign('user_name')->references('name')->on('users')->onDelete('cascade');
            $table->foreign('user_cpf')->references('cpf')->on('users')->onDelete('cascade');
            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');

            // Adiciona a chave estrangeira para o usuário que criou a reserva
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};

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
        Schema::create('pagamento__pixes', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('data');
            $table->string('cupom');
            $table->string('placa');
            $table->string('km');
            $table->string('cpf');
            $table->string('name');
            $table->string('cnpj');
            $table->string('posto');
            $table->string('produto');
            $table->string('litragem');
            $table->string('valor');
            $table->string('produto_arla');
            $table->string('litragem_arla');
            $table->string('valor_arla');
            $table->string('banco');
            $table->string('agencia');
            $table->string('conta');
            $table->string('cnpj_2');
            $table->string('favorecido');
            $table->string('pix');
            $table->string('valor_3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamento__pixes');
    }
};

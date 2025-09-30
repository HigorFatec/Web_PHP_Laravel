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
        Schema::create('fiscais', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->nullable();
            $table->string('empresa_solicitante')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('fornecedor')->nullable();
            $table->string('cnpj_fornecedor')->nullable();
            $table->string('nota_fiscal')->nullable();
            $table->string('quantidade_itens')->nullable();
            $table->string('codigo_rodopar_item')->nullable();
            $table->string('valor_devolucao')->nullable();
            $table->string('valor_nf')->nullable();
            $table->string('mercadoria_devolvida')->nullable();
            $table->string('motivo_operacao')->nullable();
            $table->text('informacoes_adicionais')->nullable();
            $table->string('cliente')->nullable();
            $table->string('cnpj_cliente')->nullable();
            $table->string('codigo_fornecedor_rodopar')->nullable();
            $table->string('filial');
            $table->string('email');
            $table->string('email_gestor');
            $table->string('tipo_pix')->nullable();
            $table->string('placa')->nullable();
            $table->string('prazo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscais');
    }
};

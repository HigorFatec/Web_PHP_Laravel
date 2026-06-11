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
        Schema::create('ajuda_de_custos', function (Blueprint $table) {
            $table->id();
            $table->integer('fornecedor');
            $table->string('cnpj');
            $table->string('name');
            $table->date('data_admissao');
            $table->decimal('valor_fixo', 15, 2);
            $table->decimal('valor_proporcional', 15, 2);
            $table->string('observacao')->nullable();
            $table->string('pix')->nullable();
            $table->string('tipo_pix')->nullable();
            $table->string('banco')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->string('favorecido')->nullable();
            $table->integer('cod_unidade');
            $table->integer('cod_gasto');
            $table->string('cod_custo');
            $table->string('approval_token')->nullable();
            $table->string('status')->default('pendente');
            $table->integer('user_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuda_de_custos');
    }
};

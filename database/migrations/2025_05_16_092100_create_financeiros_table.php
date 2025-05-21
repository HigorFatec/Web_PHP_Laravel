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
        Schema::create('financeiros', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('pedido');
            $table->string('referencia');
            $table->string('cnpj');
            $table->string('name');
            $table->string('pamcard');
            $table->string('banco');
            $table->string('agencia');
            $table->string('conta');
            $table->string('pix');
            $table->string('favorecido');
            $table->string('valor');
            $table->string('prazo');
            //reembolso
            $table->string('motivo');        
            $table->string('filial');        
            $table->string('email');        
            $table->string('email_gestor');       
            $table->string('email_gestor2');       
            $table->string('email_gestor3');       

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financeiros');
    }
};

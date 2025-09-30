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
        Schema::create('sinistros', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('name');
            $table->string('placa');
            $table->string('filial_origem');
            $table->string('email_terceiro');
            $table->string('telefone_terceiro');
            $table->string('nome_terceiro');
            $table->string('placa_terceiro');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinistros');
    }
};

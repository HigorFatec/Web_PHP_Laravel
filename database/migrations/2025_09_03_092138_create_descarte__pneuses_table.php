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
        Schema::create('descarte_pneus', function (Blueprint $table) {
            $table->id(); // id auto-incremento
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('filial_origem')->nullable();
            $table->date('data')->nullable();
            $table->time('hora')->nullable();
            $table->string('cod_pneu');
            $table->string('status_pneu')->nullable();
            $table->string('motivo_descarte')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('descarte_pneus');
    }
};

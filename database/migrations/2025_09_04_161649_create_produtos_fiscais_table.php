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
        Schema::create('produtos_fiscais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_id')->constrained('fiscais')->onDelete('cascade');
            $table->string('quantidade');
            $table->string('codigo_rodopar');
            $table->string('valor_unitario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_fiscais');
    }
};

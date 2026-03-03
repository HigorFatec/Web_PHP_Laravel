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
    Schema::create('despesas', function (Blueprint $table) {
        $table->id();
        $table->string('fornecedor');
        $table->string('despesa'); // Ex: 'Alimentação', 'Combustível'
        $table->date('date');
        $table->decimal('valor', 10, 2); // Suporta até 99.999.999,99
        $table->string('anexo')->nullable(); // Caminho do arquivo/upload
        
        // Relacionamento com o usuário
        $table->string('user_name');
        $table->string('user_email'); 

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noflies');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('notifications_custom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Dono da notificação
            $table->string('titulo');
            $table->text('mensagem');
            $table->string('url')->nullable(); // Para onde o usuário vai ao clicar
            // Coluna booleana para identificar notificações que todos devem ver
            $table->boolean('is_global')->default(false);

            $table->timestamp('read_at')->nullable(); // Se for NULL, não foi lida
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications_custom');
    }
};

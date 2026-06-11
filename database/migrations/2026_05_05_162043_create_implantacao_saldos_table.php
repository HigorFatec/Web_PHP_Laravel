<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('implantacao_saldos', function (Blueprint $table) {
            $table->id();
            
            // 🌟 O Agrupador em Lote (Une múltiplos produtos sob uma mesma requisição)
            $table->string('codigo_lote')->index(); 
            
            // Dados do Solicitante e Localização
            $table->unsignedBigInteger('user_id'); // Alterado para bater com seu Controller e tabela users
            $table->integer('cod_localizacao');
            $table->string('descricao_localizacao')->nullable();
            
            // Dados de Controle e Fluxo
            $table->string('movimentacao'); // Entrada ou Saída
            $table->string('status')->default('em análise'); // em análise, finalizado, reprovado
            $table->string('observacao')->nullable();
            $table->string('posicao')->nullable();
            
            // Dados do Produto Específico desta linha
            $table->integer('produto'); // Código do produto
            $table->string('descricao_produto');
            $table->integer('grupo');
            $table->integer('subgrupo');
            $table->decimal('quantidade', 15, 4); // Decimal é melhor para aceitar frações/pesos
            
            // Dados Financeiros calculados pelo ERP (Cruciais para as telas e e-mail)
            $table->decimal('saldo_fisico', 15, 4)->default(0);
            $table->decimal('valor_medio', 15, 2)->default(0);
            $table->decimal('valor_total', 15, 2)->default(0);

            // Fluxo de Responsáveis (E-mails dos aprovadores)
            $table->string('gestor_filial');
            $table->string('gestor_regional');
            $table->string('diretor');

            // 🌟 Status de Aprovações (Devem aceitar NULL para indicar "Aguardando resposta")
            $table->boolean('aprovacao_filial')->nullable()->default(null);
            $table->boolean('aprovacao_regional')->nullable()->default(null);
            $table->boolean('aprovacao_diretoria')->nullable()->default(null);

            $table->timestamps();
            
            // Chave estrangeira opcional (se sua tabela de usuários padrão for 'users')
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('implantacao_saldos');
    }
};
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\UnidadesNegocio;
use App\Models\GestorFinanceiro;


class Financeiro extends Model
{
    use HasFactory;
    protected $fillable = [
        'tipo',
        'pedido',
        'referencia',
        'cnpj',
        'name',
        'pamcard',
        'banco',
        'agencia',
        'conta',
        'pix',
        'favorecido',
        'valor',
        'prazo',
        'motivo',
        'filial',
        'email',
        'email_gestor',
        'tipo_pix',
        'placa',
        'prazo',
        'socorro_em_rota',
        'solicitante',
        'fornecedor',
        'cod_unidade',
        'cod_custo',
        'cod_gasto',
        'tem_nota_fiscal',
        'gestor_aprovador',
        'id_raz',
        'anexo_path',
        'status',
        'emails',
        'approval_token',
        'comprovante_pagamento',
        'motivo_reprovacao',
        'adiantamento_fornecedor',
        'user_id',
        'relatorio_id',
        'despesas_selecionadas[]',
        'frota_bloqueada',
        'motivo_rejeicao_interno',
        'pix_flow_id'

    ];

// Relacionamento com o unidade_negocio
public function unidades()
{
    return $this->belongsTo(UnidadesNegocio::class, 'cod_unidade', 'cod_unidade'); 
    // 'cod_unidade' é a coluna em financeiro
    // 'cod_unidade' é a coluna correspondente em unidades_negocio
}

public function centroGasto()
{
    return $this->belongsTo(UnidadesNegocio::class, 'cod_gasto', 'cod_gasto');
}

public function centroCusto()
{
    return $this->belongsTo(UnidadesNegocio::class, 'cod_custo', 'cod_custo');
}

public function gestorFinanceiro()
{
    return $this->belongsTo(GestorFinanceiro::class, 'gestor_aprovador','email_gestor');
}

public function unidadeAprovadora()
{
    return $this->belongsTo(UnidadesNegocio::class, 'gestor_aprovador', 'email_gestor');
}

public function despesas()
{
    return $this->hasMany(Reembolso::class, 'relatorio_id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}


public static function unidade_de_negocio() {
    return DB::connection ('sqlsrv')
    ->table('RODUNN')
    ->select(
        'CODUNN', 'DESCRI'
        )
    ->where('SITUAC', 'A')
    ->orderBy('DESCRI')
    ->get();
} 

public static function centro_de_custo() {
    return DB::connection ('sqlsrv')
    ->table('RODCUS')
    ->select(
        'CODCUS', 'DESCRI'
        )
    ->where('SITUAC', 'A')
    ->orderBy('DESCRI')
    ->get();
}

public static function centro_de_gasto() {
    return DB::connection ('sqlsrv')
    ->table('RODCGA')
    ->select(
        'CODCGA', 'DESCRI'
        )
    ->where('SITUAC', 'A')
    ->orderBy('DESCRI')
    ->get();
}


public function financeiroAvista($id, $valor, $solicitante, $fornecedor, $pedido,$placa,$prazo, $filial, $conta){


    // 1. Inicia uma transação no SQL Server
    DB::connection('sqlsrv')->beginTransaction();

    try{

    // 2. BUSCA O ID E BLOQUEIA A TABELA (lockForUpdate)
    // Isso diz ao SQL: "Estou lendo este valor e ninguém mais pode ler ou gravar até eu dar commit"
    $ultimo = DB::connection('sqlsrv')->table('BANRAZ')
                ->lockForUpdate() 
                ->orderBy('ID_RAZ', 'desc')
                ->first();

    $novo_id = ($ultimo ? $ultimo->ID_RAZ : 0) + 1;

    
    $id = (int) $id;
    $valor = (float) $valor;

    $saldo_anterior = DB::connection('sqlsrv')
        ->table('BANRAZ')
        ->orderByDesc('ID_RAZ')
        ->value('SLDATU');  // pega somente o valor

    $saldo_anterior = (float) $saldo_anterior;

    $saldo_atualizado = $saldo_anterior - (float) $valor;

    $filial = 5;

    // Primeiro, buscamos o código da filial do pedido
    $codFil = DB::connection('sqlsrv')
                ->table('ESTPED')
                ->where('NUMPED', $pedido)
                ->value('CODFIL');


    // Agora sim, fazemos a lógica de padronização das contas
    if (in_array($codFil, [37, 38])) {
        $conta = '13202-4';
    } elseif ($codFil == 40) {
        $conta = '181646-2';
    } else {
        $conta = '39020-5';
    }



DB::connection('sqlsrv')->table('BANRAZ')->insert([
    'ID_RAZ' => DB::raw('(SELECT MAX(ID_RAZ) + 1 FROM BANRAZ)'),
    'NUMDOC' => $pedido,
    'CODCTA' => $conta,
    'TIPDOC' => 'ADF',
    'CODFIL' => DB::raw("(SELECT CODFIL FROM ESTPED WHERE NUMPED = {$pedido})"),
    'TIPORI' => 'ADF',
    'CODBCO' => 341,
    'CODHISBC' => 124,
    'CODPAD' => 1,
    'ORIGEM' => 'LB',
    'DATREF' => Carbon::now()->format('m/d/Y'),
    'DATDOC' => Carbon::now()->format('m/d/Y'),
    'VLRDOC' => $valor,
    'DEBCRE' => 'D',
    'DATCOM' => Carbon::now()->format('m/d/Y'),
    'SITUAC' => 'I',
    'SLDANT' => $saldo_anterior,
    'SLDATU' => $saldo_atualizado,
    'OBSERV' => 'Socorro em Rota - Formulário Financeiro - aprovado por '.$prazo,
    'COMPEN' => 'S',
    'CODTAR' => 1687,
    'DATATU' => DB::raw('GETDATE()'),
    'USUATU' => 'IMPORTACAO',
    'USUINC' => 'IMPORTACAO',
    'CTATRA' => NULL, 
    'DATINC' => DB::raw('GETDATE()'),
    'CODCLIFOR' => DB::raw("(SELECT CODCLIFOR FROM ESTPED WHERE NUMPED = {$pedido})"),
    'BLOQUE' => 'N',
    'NUMPED' => NULL,
    'SOLICI' => $solicitante,
    'VLRITX' => 0,
    'VLRITX_TRA' => 0,
    'FINALI' => $placa,

]);

DB::connection('sqlsrv')->table('BANRAT')->insert([
    'ID_BANRAT' => DB::raw('(SELECT MAX(ID_BANRAT) + 1 FROM BANRAT)'),
    'NUMDOC' => $id,
    'CODCTA' => $conta,
    'TIPDOC' => 'ADF',
    'CODFIL' => DB::raw("(SELECT CODFIL FROM ESTPED WHERE NUMPED = {$pedido})"),
    'CODUNN' => 19,
    'CODCGA' => 59,
    'CODCUS' => '102',
    'SINTET' => 373,
    'ANALIT' => 374,
    'VALOR' => $valor,
    'DATATU' => DB::raw('GETDATE()'),
    'USUATU' => 'IMPORTACAO',
    'DATINC' => DB::raw('GETDATE()'),
    'ID_RAZ' => DB::raw('(SELECT MAX(ID_RAZ) FROM BANRAZ)'),

]);

DB::connection('sqlsrv')->table('BANRAZ')
    ->where('ID_RAZ', DB::raw('(SELECT MAX(ID_RAZ) FROM BANRAZ)'))
    ->update([
    'SITUAC' => 'O',
]);

DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

} catch (\Exception $e) {
    DB::connection('sqlsrv')->rollBack();
    throw $e;
}
}


public function pagdoc($fornecedor,$valor,$id,$codunn,$codcus,$codgas,$prazo,$analitica,$sintetica){

// 1. Inicia uma transação no SQL Server
DB::connection('sqlsrv')->beginTransaction();

try{

    $id = (string) $id;
    $valor = (float) $valor;
    $codunn = (int) $codunn;
    $codcus = (string) $codcus;
    $codgas = (int) $codgas;

    $analitica = (int) $analitica;
    $sintetica = (int) $sintetica;



    DB::connection('sqlsrv')->table('PAGDOC')->insert([
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'TIPDOC' => 'RCB',
        'CODFIL' => 5,
        'CODPAD' => 1,
        'CODTAX' => 1,
        'CODBCO' => 237,
        'DATEMI' => DB::raw('GETDATE()'),
        'DATREF' => DB::raw('GETDATE()'),
        'ORIGEM' => 'N',
        'SITUAC' => 'I',
        'VLRDOC' => $valor,
        'VLRLIQ' => $valor,
        'VLRPAG' => 0,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
        'REFERE' => 'IMPORTACAO FORMULARIO FINANCEIRO, Aprovado por '.$prazo,
        'USUINC' => 'IMPORTACAO',
        'DATINC' => DB::raw('GETDATE()'),


    ]);

    DB::connection('sqlsrv')->table('PAGTRI')->insert([
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'CODFIL' => 5,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
        'USUINC' => 'IMPORTACAO',
        'DATINC' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->table('PAGDOCI')->insert([
        'ID_PAGDOCI' => DB::raw('(SELECT MAX(ID_PAGDOCI) + 1 FROM PAGDOCI)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'NUMPAR' => 1,
        'DATVEN' => Carbon::now()->format('m/d/Y'),
        'DATPRE' => Carbon::now()->format('m/d/Y'),
        'DATINC' => Carbon::now()->format('m/d/Y'),
        'DATPAG' => Carbon::now()->format('m/d/Y'),        
        'SITUAC' => 'D',
        'VLRPAR' => $valor,
        'VLRPAG' => 0,
        'VLRLIQ' => $valor,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->table('PAGRAT')->insert([
        'ID_PAGRAT' => DB::raw('(SELECT MAX(ID_PAGRAT) + 1 FROM PAGRAT)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'CODUNN' => $codunn,
        'CODCUS' => $codcus,
        'CODCGA' => $codgas,
        'SINTET' => $sintetica,
        'ANALIT' => $analitica,
        'VALOR' => $valor,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
        'DATINC' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->table('PAGMEN')->insert([
        'NUMLAN' => DB::raw('(SELECT MAX(NUMLAN) + 1 FROM PAGMEN)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'CODFIL' => 5,
        'CODHISPG' => 2,
        'DATLAN' => DB::raw('GETDATE()'),
        'SITUAC' => 'L',
        'NUMPAR' => 1,
        'TIPDOC' => 'RCB',
        'CODTAX' => 1,
        'DEBCRE' => 'C',
        'VLRLAN' => $valor,
        'DATINC' => DB::raw('GETDATE()'),
        'DATATU' => DB::raw('GETDATE()'),
        'USUATU' => 'IMPORTACAO',
    ]);

    DB::connection('sqlsrv')->table('PAGDOC')
    ->where('NUMDOC', $id)
    ->where('SERIE', 'A')
    ->where('CODCLIFOR', $fornecedor)
    ->update([
        'SITUAC' => 'D',
    ]);

    DB::connection('sqlsrv')->table('OSEONF')
    ->where('NUMDOC', $id)
    ->where('SERIE', 'A')
    ->where('CODCLIFOR', $fornecedor)
    ->update([
        'VLRDOC' => $valor,
    ]);

DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

} catch (\Exception $e) {
    DB::connection('sqlsrv')->rollBack();
    throw $e;
}
}







// PROCESSO DE REEMBOLSO

public function finalizar($fornecedor,$valor,$id,$codunn,$codcus,$codgas,$prazo,$valorLiquido,$valorUtilizado,$situacao){


// 1. Inicia uma transação no SQL Server
DB::connection('sqlsrv')->beginTransaction();

try{

    $id = (string) $id;
    $valor = (float) $valor;
    $codunn = (int) $codunn;
    $codcus = (string) $codcus;
    $codgas = (int) $codgas;


    DB::connection('sqlsrv')->table('PAGDOC')->insert([
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'TIPDOC' => 'REE',
        'CODFIL' => 5,
        'CODPAD' => 1,
        'CODTAX' => 1,
        'CODBCO' => 237,
        'DATEMI' => DB::raw('GETDATE()'),
        'DATINC' => DB::raw('GETDATE()'),
        'DATREF' => DB::raw('GETDATE()'),
        'ORIGEM' => 'N',
        'SITUAC' => 'I',
        'DESADT' => $valorUtilizado,
        'VLRDOC' => $valor,
        'VLRIND' => $valor,
        'VLRLIQ' => $valorLiquido,
        'VLRPAG' => 0,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
        'REFERE' => 'IMPORTACAO FORMULARIO REEMBOLSO, Aprovado por '.$prazo,
        'USUINC' => 'IMPORTACAO',


    ]);

    DB::connection('sqlsrv')->table('PAGTRI')->insert([
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'CODFIL' => 5,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->table('PAGDOCI')->insert([
        'ID_PAGDOCI' => DB::raw('(SELECT MAX(ID_PAGDOCI) + 1 FROM PAGDOCI)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'NUMPAR' => 1,
        'DATVEN' => Carbon::now()->format('m/d/Y'),
        'DATPRE' => Carbon::now()->format('m/d/Y'),
        'DATINC' => Carbon::now()->format('m/d/Y'),
        'DATPAG' => Carbon::now()->format('m/d/Y'),
        'SITUAC' => $situacao,
        'VLRPAR' => $valor,
        'VLRPAG' => 0,
        'DESADT' => $valorUtilizado,
        'VLRLIQ' => $valorLiquido,
        'VLRIND' => $valor,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),

    ]);


    DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

} catch (\Exception $e) {
    DB::connection('sqlsrv')->rollBack();
    throw $e;
}
}








public function finalizar_2($fornecedor,$valor,$id,$codunn,$codcus,$codgas,$prazo,$valorLiquido,$valorUtilizado,$situacao){


// 1. Inicia uma transação no SQL Server
DB::connection('sqlsrv')->beginTransaction();

try{

    $id = (string) $id;
    $valor = (float) $valor;
    $codunn = (int) $codunn;
    $codcus = (string) $codcus;
    $codgas = (int) $codgas;

    if($situacao !== 'D'){

    DB::connection('sqlsrv')->table('PAGMEN')->insert([
        'NUMLAN' => DB::raw('(SELECT MAX(NUMLAN) + 1 FROM PAGMEN)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'CODFIL' => 5,
        'CODHISPG' => 2,
        'DATLAN' => DB::raw('GETDATE()'),
        'SITUAC' => 'L',
        'NUMPAR' => 1,
        'TIPDOC' => 'ACV',
        'CODTAX' => 1,
        'DEBCRE' => 'C',
        'VLRLAN' => $valor,
        'DATINC' => DB::raw('GETDATE()'),
        'DATATU' => DB::raw('GETDATE()'),
        'USUATU' => 'IMPORTACAO',
    ]);
    }

    DB::connection('sqlsrv')->table('PAGDOC')
    ->where('NUMDOC', $id)
    ->where('SERIE', 'A')
    ->where('CODCLIFOR', $fornecedor)
    ->update([
        'SITUAC' => $situacao,
    ]);

    DB::connection('sqlsrv')->table('OSEONF')
    ->where('NUMDOC', $id)
    ->where('SERIE', 'A')
    ->where('CODCLIFOR', $fornecedor)
    ->update([
        'VLRDOC' => $valor,
    ]);



    DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

} catch (\Exception $e) {
    DB::connection('sqlsrv')->rollBack();
    throw $e;
}
}

}
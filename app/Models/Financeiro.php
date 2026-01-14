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




public function financeiroAvista($id, $valor, $solicitante, $fornecedor, $pedido,$placa,$prazo, $filial, $conta){

    $id = (int) $id;
    $valor = (float) $valor;

    $saldo_anterior = DB::connection('sqlsrv')
        ->table('BANRAZ')
        ->orderByDesc('ID_RAZ')
        ->value('SLDATU');  // pega somente o valor

    $saldo_anterior = (float) $saldo_anterior;

    $saldo_atualizado = $saldo_anterior - (float) $valor;

    $filial = 5;



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
    'DATCOM' => NULL,
    'SITUAC' => 'I',
    'SLDANT' => $saldo_anterior,
    'SLDATU' => $saldo_atualizado,
    'OBSERV' => 'Socorro em Rota - Formulário Financeiro - aprovado por '.$prazo,
    'COMPEN' => 'N',
    'CODTAR' => 1687,
    'DATATU' => DB::raw('GETDATE()'),
    'USUATU' => 'Importacao',
    'USUINC' => 'Importacao',
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
    'USUATU' => 'Importacao',
    'DATINC' => DB::raw('GETDATE()'),
    'ID_RAZ' => DB::raw('(SELECT MAX(ID_RAZ) FROM BANRAZ)'),

]);

DB::connection('sqlsrv')->table('BANRAZ')
    ->where('ID_RAZ', DB::raw('(SELECT MAX(ID_RAZ) FROM BANRAZ)'))
    ->update([
    'SITUAC' => 'P',
]);

}


public function pagdoc($fornecedor,$valor,$id,$codunn,$codcus,$codgas,$prazo){

    $id = (string) $id;
    $valor = (float) $valor;
    $codunn = (int) $codunn;
    $codcus = (string) $codcus;
    $codgas = (int) $codgas;



    DB::connection('sqlsrv')->table('PAGDOC')->insert([
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $fornecedor . '-' . $id,
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
        'USUATU' => 'Importacao',
        'DATATU' => DB::raw('GETDATE()'),
        'REFERE' => 'IMPORTACAO FORMULARIO FINANCEIRO, Aprovado por '.$prazo,

    ]);

    DB::connection('sqlsrv')->table('PAGTRI')->insert([
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $fornecedor . '-' . $id,
        'CODFIL' => 5,
        'USUATU' => 'Importacao',
        'DATATU' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->table('PAGDOCI')->insert([
        'ID_PAGDOCI' => DB::raw('(SELECT MAX(ID_PAGDOCI) + 1 FROM PAGDOCI)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $fornecedor . '-' . $id,
        'NUMPAR' => 1,
        'DATVEN' => Carbon::now()->format('m/d/Y'),
        'SITUAC' => 'D',
        'VLRPAR' => $valor,
        'VLRPAG' => 0,
        'VLRLIQ' => $valor,
        'USUATU' => 'Importacao',
        'DATATU' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->table('PAGRAT')->insert([
        'ID_PAGRAT' => DB::raw('(SELECT MAX(ID_PAGRAT) + 1 FROM PAGRAT)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $fornecedor . '-' . $id,
        'CODUNN' => $codunn,
        'CODCUS' => $codcus,
        'CODCGA' => $codgas,
        'SINTET' => 83,
        'ANALIT' => 376,
        'VALOR' => $valor,
        'USUATU' => 'Importacao',
        'DATATU' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->table('PAGMEN')->insert([
        'NUMLAN' => DB::raw('(SELECT MAX(NUMLAN) + 1 FROM PAGMEN)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $fornecedor . '-' . $id,
        'CODFIL' => 5,
        'CODHISPG' => 1,
        'DATLAN' => DB::raw('GETDATE()'),
        'DEBCRE' => 'C',
        'VLRLAN' => $valor,
        'DATATU' => DB::raw('GETDATE()'),
        'USUATU' => 'Importacao',
    ]);

    DB::connection('sqlsrv')->table('PAGDOC')
    ->where('NUMDOC', $fornecedor . '-' . $id)
    ->where('SERIE', 'A')
    ->where('CODCLIFOR', $fornecedor)
    ->update([
        'SITUAC' => 'D',
    ]);

    DB::connection('sqlsrv')->table('OSEONF')
    ->where('NUMDOC', $fornecedor . '-' . $id)
    ->where('SERIE', 'A')
    ->where('CODCLIFOR', $fornecedor)
    ->update([
        'VLRDOC' => $valor,
    ]);

}


}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Rdv extends Model
{
    use HasFactory;

    protected $table = 'despesas';

    protected $fillable = [
        'id',
        'fornecedor',
        'descricao_fornecedor',
        'despesa',
        'descricao_despesa',
        'date',
        'valor',
        'anexo',
        'user_id',
        'user_name',
        'user_email',
        'status',
        'approval_token',
        'observacao',
        'SINTET',
        'ANALIT'
    ];

    // É bom definir o inverso da relação também
    public function relatorio()
    {
        return $this->belongsTo(Relatorio::class, 'relatorio_id');
    }



    public static function produtos (){


    return DB::connection('sqlsrv')
        ->table('ESTPRO')
        ->select('*')
        ->where('CODGPP', 21)
        ->orderBy('DESCRI', 'desc')
        ->get();

    }

    //Relacionamento com a tabela usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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






public function produtosMateriais($id, $fornecedor, $produto, $valor,$ultimoId,$relatorio_id, $x){

    $id = (string) $id;
    $fornecedor = (string) $fornecedor;
    $produto = (string) $produto;

    $valor = (float) $valor;

    $quantidade = 1;


    // 1. Inicia uma transação no SQL Server
    DB::connection('sqlsrv')->beginTransaction();



    try{

    DB::connection('sqlsrv')->table('ESTAIE')->insert([
        'ID_AIE' => $ultimoId,
        'CODCLIFOR'=> $fornecedor,
        'TIPONF' => 'REL',
        'SERIE' => 'A',
        'NUMDOC' => $relatorio_id,
        'CODPROD' => $produto,
        'QTDENT' => $quantidade,
        'VLRUNI' => $valor,
        'VLRTOT' => $valor,
        'VLRIPI' => 0,
        'PRICMS' => 0,
        'PRDCMR' => NULL,
        'DATATU' => DB::raw('GETDATE()'),
        'USUATU' => 'IMPORTACAO',
        'NUMPED' => NULL,
        'VLRPIS' => 0.00,
        'VLRCOF' => 0.00,
        'PERIPI' => 0,
        'DESCRI' => DB::raw("(SELECT DESCRI FROM ESTPRO WHERE CODPROD = {$produto})"),
        'CODFIS' => 1102,
        'BASSUB' => NULL,
        'REFINT'=> NULL,
        'REFFOR'=> NULL,
        'VLRFRE'=> NULL,
        'DESPIN'=> NULL,
        'VLRSEG'=> NULL,
        'BASCAL' => 0,
        'VLRICM' => 0,
        'CSTICM' => '90',
        'OUTROS' => 0,
        'VLRISE' => $valor,
        'DIFALI'=> NULL,
        'VLRDIF'=> NULL,
        'ALISUB'=> NULL,
        'ICMSUB'=> NULL,
        'CSTSUB'=> '90',
        'BASIPI'=> NULL,
        'CSTIPI' => '03',
        'BASPIS' => 0,
        'CSTPIS' => '70',
        'BASCOF' => 0,
        'CSTCOF' => '70',
        'CODGRUFIS' => 30,
        'DESTAC' => 'N',
        'CODCHA'=> NULL,
        'CODDEP'=> NULL,
        'CODAUT'=> NULL,
        'CMBAMB'=> NULL,
        'UFCONS'=> NULL,
        'BASCID'=> NULL,
        'PERCID'=> NULL,
        'VLRCID'=> NULL,
        'OBSFIS'=> NULL,
        'CODVEI'=> NULL,
        'VLRIMP'=> NULL,
        'TOTPAR'=> NULL,
        'ALICOT'=> NULL,
        'VCTPAR'=> NULL,
        'DEPCAL'=> NULL,
        'ANALIT'=> NULL,
        'ORDEM' => $x,
        'ICMDED'=> NULL,
        'UNIDAD'=> NULL,
        'BASCBS'=> NULL,
        'ALICBS'=> NULL,
        'VLRCBS'=> NULL,
        'CSTCBS'=> NULL, 
        'CLACBS'=> NULL,
        'BASIBS'=> NULL, 
        'ALIIBS'=> NULL, 
        'VLRIBS'=> NULL, 
        'CSTIBS'=> NULL, 
        'CLAIBS'=> NULL, 
        'BASIS'=> NULL, 
        'ALIIS'=> NULL, 
        'VLRIS'=> NULL, 
        'CSTIS'=> NULL, 
        'CLAIS'=> NULL,
        'ALIREM' => 0,

    ]);


    DB::connection('sqlsrv')->table('ESTAIL')->insert([
        'ID_AIE'     => $ultimoId,
        'CODCLIFOR'  => $fornecedor,
        'TIPONF'     => 'REL',
        'SERIE'      => 'A',
        'NUMDOC'     => $relatorio_id,
        'CODPROD'    => $produto,
        'QUANTI'     => 1,
        'CODLOC'     => 1,
        'DATATU'     => DB::raw('GETDATE()'), // Usa a data do SQL Server
        'DATINC'     => DB::raw('GETDATE()'),
        'USUATU'     => 'IMPORTACAO'
    ]);

    DB::connection('sqlsrv')->table('ESTPRO')
        ->where('CODPROD', $produto)
        ->update([
            'PREMED' => $valor,
            'PRECUS' => $valor,
            'ULTFOR' => $fornecedor,
            'ULTCOM' => DB::raw("CONVERT(VARCHAR(10), GETDATE(), 101)"),
            'ULTPRE' => $valor,
            'PRECON' => $valor
        ]);

    DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário


    } catch (\Exception $e) {
        DB::connection('sqlsrv')->rollBack();
        throw $e;
    }
}



public function pagrat($fornecedor,$valor,$id,$codunn,$codcus,$codgas,$produto,$sintet,$analit){


// 1. Inicia uma transação no SQL Server
DB::connection('sqlsrv')->beginTransaction();

try{

    $id = (string) $id;
    $valor = (float) $valor;
    $codunn = (int) $codunn;
    $codcus = (string) $codcus;
    $codgas = (int) $codgas;

        // 🔎 Verifica duplicidade e ajusta valor
    while (
        DB::connection('sqlsrv')->table('PAGRAT')
            ->where('CODCLIFOR', $fornecedor)
            ->where('NUMDOC', $id)
            ->where('VALOR', $valor)
            ->exists()
    ) {
        $valor += 0.01; // soma 1 centavo até ficar único
    }

    DB::connection('sqlsrv')->table('PAGRAT')->insert([
        'ID_PAGRAT' => DB::raw('(SELECT MAX(ID_PAGRAT) + 1 FROM PAGRAT)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'CODUNN' => $codunn,
        'CODCUS' => $codcus,
        'CODCGA' => $codgas,
        'SINTET' => $sintet,
        'ANALIT' => $analit,
        'VALOR' => $valor,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
    ]);

    DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

} catch (\Exception $e) {
    DB::connection('sqlsrv')->rollBack();
    throw $e;
}
}


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Adiantamento;
use Carbon\Carbon;




class Relatorio extends Model
{
    use HasFactory;

    protected $table = 'relatorio';

    protected $fillable = [
        'titulo',
        'motivo',
        'cod_unidade',
        'cod_custo',
        'cod_gasto',
        'gestor_aprovador',
        'inicio_viagem',
        'fim_viagem',
        'despesas_selecionadas[]',
        'user_id',
        'user_name',
        'user_email',
        'status',
        'approval_token',
        'valor',
        'id_rodopar'
    ];

    public function despesas()
    {
        return $this->hasMany(Rdv::class, 'relatorio_id');
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







public function entradaMateriais($id, $valor, $fornecedor,$prazo){


    // 1. Inicia uma transação no SQL Server
    DB::connection('sqlsrv')->beginTransaction();

    try{

    
    $id = (int) $id;
    $valor = (float) $valor;
    $fornecedor = (string) $fornecedor;




    $filial = 5;



    DB::connection('sqlsrv')->table('ESTENT')->insert([
    'DATINC' => DB::raw('GETDATE()'),
    'USUINC' => 'IMPORTACAO',
    'CODPAD' => 1,
    'USUATU' => 'IMPORTACAO',
    'DATATU' => DB::raw('GETDATE()'),
    'CODMOT' => NULL,
    'CODFEC' => NULL,
    'NUMDFE' => NULL,
    'CODLIN' => NULL,
    'REFSER' => NULL,
    'REFDOC' => 0,
    'CODLOT' => 0,
    'RESPFR' => 'S',
    'SUBCON' => 'N',
    'CODTPS' => NULL,
    'CODMUN' => NULL,
    'CLIREB' => NULL,
    'NFE_ID' => NULL,
    'CODOPE' => NULL,
    'TIPONF' => 'REL',
    'CODTAR' => 1717,
    'CODFPG' => NULL,
    'SITUAC' => 'I',
    'CODBCO' => 237,
    'CODTAX' => 1,
    'CODCLIFOR' => $fornecedor,
    'REFERE' => NULL,
    'NUMDOC' => $id,
    'CODFIL' => $filial,
    'OBSERV' => 'Reembolso de Despesa - Aprovado por ' .$prazo,
    'SERIE' => 'A',
    'VLRDOC' => $valor,
    'DATEMI' => DB::raw("CONVERT(VARCHAR(10), GETDATE(), 101)"),
    'DATREF' => DB::raw("CONVERT(VARCHAR(10), GETDATE(), 101)"),
    'VLRLIQ' => $valor,
    'DESCAN' => 0,
    'JURDIA' => 0,
    'VLRPED' => 0,
    'DESPEX' => 0,
    'ICMSEX' => 0,
    'PERJUR' => 0,
    'DESISS' => 0,
    'DESPIS' => 0,
    'DESIR' => 0,
    'DESINS' => 0,
    'DESCSL' => 0,
    'DESCOF' => 0,
    'ICMSST' => 0,
    'DESADT' => 0,
    'DESIPI' => 0,
    'REFDAT' => NULL,
    'DEDBAS' => 0,
    'DESPIN' => 0,
    'DESCOB' => 0,
    'VLRFRE' => 0,
    'VLRSEG' => 0,
    'NUMCNO' => NULL,

    ]);



    // DB::connection('sqlsrv')->table('PAGTRI')->insert([
    //     'CODCLIFOR' => $fornecedor,
    //     'SERIE' => 'A',
    //     'NUMDOC' => $id,
    //     'CODFIL' => $filial,
    //     'BASINS' => 5,
    //     'BASINS' => 0,
    //     'ALIINS'=> 0,
    //     'DESINS'=> 0,
    //     'CODINS'=> 0,
    //     'BASIR'=> 0,
    //     'ALIIR'=> 0,
    //     'DESIR' => 0,
    //     'CODIR'=> 0,
    //     'BASISS'=> 0,
    //     'ALIISS'=> 0,
    //     'DESISS'=> 0,
    //     'CODISS'=> 0,
    //     'BASPIS'=> 0,
    //     'ALIPIS'=> 0,
    //     'DESPIS'=> 0,
    //     'CODPIS'=> 0,
    //     'BASCOF'=> 0,
    //     'ALICOF' => 0,
    //     'DESCOF'=> 0,
    //     'CODCOF'=> 0,
    //     'BASCSL'=> 0,
    //     'ALICSL'=> 0,
    //     'DESCSL'=> 0,
    //     'CODCSL'=> 0,
    //     'DATINC'=> DB::raw('GETDATE()'),
    //     'USUINC'=> 'Importacao',
    //     'DATATU' => DB::raw('GETDATE()'),
    //     'USUATU' => 'Importacao',

    // ]);
    

    DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

    } catch (\Exception $e) {
        DB::connection('sqlsrv')->rollBack();
        throw $e;
    }
}




public function adiantamentos($fornecedor, $id, $valorRelatorio, $id_raz) {
    
    $adiantamento = Adiantamento::where('fornecedor', $fornecedor)
        ->where('status', 'aprovado')
        ->orderBy('id','desc')
        ->first();

    if (!$adiantamento) return;

    // 1. Inicia transação nos DOIS bancos
    DB::beginTransaction(); // Banco Padrão (MySQL)
    DB::connection('sqlsrv')->beginTransaction(); // SQL Server

    try {
        // Busca saldo no SQL Server
        $valorOriginal = DB::connection('sqlsrv')
            ->table('BANRAZ')
            ->where('ID_RAZ', $id_raz)
            ->where('SITUAC', '<>', 'I')
            ->value('VLRDOC') ?? 0;

        if ($valorOriginal <= 0) {
            DB::rollBack();
            DB::connection('sqlsrv')->rollBack();
            return;
        }

        $valorJaUtilizado = DB::connection('sqlsrv')
            ->table('BANRNF')
            ->where('ID_RAZ', $id_raz)
            ->sum('VLRDOC');

        $saldoRealDisponivel = $valorOriginal - $valorJaUtilizado;

        if ($saldoRealDisponivel <= 0) {
            DB::rollBack();
            DB::connection('sqlsrv')->rollBack();
            return;
        }

        $valorParaAbater = min($valorRelatorio, $saldoRealDisponivel);
        $valorLiquido = $valorRelatorio - $valorParaAbater;

        // Determina situação local
        $situacaoLocal = ($valorRelatorio <= $saldoRealDisponivel) ? 'L' : 'P';

        // Atualiza banco padrão IMEDIATAMENTE
        $adiantamento->update([
            'situac' => $situacaoLocal,
            'valor_liquido' => $valorLiquido,
            'valor_utilizado' => $valorParaAbater
        ]);

        if ($valorParaAbater > 0) {
            // Atualiza SQL Server
            DB::connection('sqlsrv')->table('ESTENT')
                ->where('NUMDOC', $id)
                ->where('CODCLIFOR', $fornecedor)
                ->where('TIPONF', 'REL')
                ->where('SERIE', 'A')
                ->update(['DESADT' => $valorParaAbater, 'VLRLIQ' => $valorLiquido]);

            DB::connection('sqlsrv')->table('BANRNF')->insert([
                'CODCLIFOR' => $fornecedor,
                'NUMDOC'    => $id,
                'TIPONF'    => 'REL',
                'SERIE'     => 'A',
                'SITNOT'    => 'O',
                'ID_RAZ'    => $id_raz,
                'VLRDOC'    => $valorParaAbater,
                'DATATU'    => DB::raw('GETDATE()'),
                'USUATU'    => 'IMPORTACAO',
                'ORIGEM'    => 'NFE',
                'NOMEPC'    => 'Webserver'
            ]);
        }

        // Commits
        DB::connection('sqlsrv')->commit();
        DB::commit(); 

        // ADICIONE ISSO AQUI:
        $adiantamento->refresh(); // Atualiza os dados do banco para o objeto
        return $adiantamento;    // Devolve o objeto "fresco" para o Controller

    } catch (\Exception $e) {
        DB::connection('sqlsrv')->rollBack();
        DB::rollBack();
        throw $e;
    }
}




public function finalizar($fornecedor,$valor,$id,$codunn,$codcus,$codgas,$prazo,$valorLiquido,$valorUtilizado,$situacao){


// 1. Inicia uma transação no SQL Server
DB::connection('sqlsrv')->beginTransaction();

try{

    $id = (string) $id;
    $valor = (float) $valor;
    $codunn = (int) $codunn;
    $codcus = (string) $codcus;
    $codgas = (int) $codgas;


    DB::connection('sqlsrv')->table('ESTENT')
    ->where('NUMDOC', $id)
    ->where('CODCLIFOR', $fornecedor)
    ->where('SERIE', 'A')
    ->where('TIPONF', 'REL')
    ->update([
        'SITUAC' => 'O'
    ]);

    DB::connection('sqlsrv')->table('PAGDOC')->insert([
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'TIPDOC' => 'ACV',
        'CODFIL' => 5,
        'CODPAD' => 1,
        'CODTAX' => 1,
        'CODBCO' => 237,
        'DATEMI' => DB::raw('GETDATE()'),
        'DATREF' => DB::raw('GETDATE()'),
        'ORIGEM' => 'E',
        'SITUAC' => 'I',
        'DESADT' => $valorUtilizado,
        'VLRDOC' => $valor,
        'VLRIND' => $valor,
        'VLRLIQ' => $valorLiquido,
        'VLRPAG' => 0,
        'USUATU' => 'IMPORTACAO',
        'DATATU' => DB::raw('GETDATE()'),
        'REFERE' => 'IMPORTACAO FORMULARIO DESPESAS, Aprovado por '.$prazo,

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


    DB::connection('sqlsrv')->table('PAGMEN')->insert([
        'NUMLAN' => DB::raw('(SELECT MAX(NUMLAN) + 1 FROM PAGMEN)'),
        'CODCLIFOR' => $fornecedor,
        'SERIE' => 'A',
        'NUMDOC' => $id,
        'CODFIL' => 5,
        'CODHISPG' => 1,
        'DATLAN' => DB::raw('GETDATE()'),
        'DEBCRE' => 'C',
        'VLRLAN' => $valor,
        'DATATU' => DB::raw('GETDATE()'),
        'USUATU' => 'IMPORTACAO',
    ]);

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
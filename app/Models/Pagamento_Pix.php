<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Produto_Arla;
use Carbon\Carbon;


class Pagamento_Pix extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'data',
        'cupom',
        'placa',
        'km',
        'cpf',
        'name',
        'cnpj',
        'posto',
        'produto',
        'litragem',
        'valor',
        'produto_arla',
        'litragem_arla',
        'valor_arla',
        'banco',
        'agencia',
        'conta',
        'cnpj_2',
        'favorecido',
        'pix',
        'valor_3',
        'email_gestor',
        'filial',
        'approval_token',
        'anexo_path',
        'status',
        'fornecedor',
        'id_raz',
        'codaba',
        'solicitante'
    ];

    // Relacionamento com o gestor
    public function produtos()
    {
        return $this->belongsTo(Produto_Arla::class, 'produto', 'id'); 
    }

        // Relacionamento com o gestor
    public function produtos_arla()
    {
        return $this->belongsTo(Produto_Arla::class, 'produto_arla', 'id'); 
    }


public static function veiculos() {
    return DB::connection('sqlsrv')
        ->table('RODVEI')
        ->select('CODVEI', 'NUMVEI')
        ->where('SITUAC', '<>', '3')
        ->orderBy('NUMVEI')
        ->get();
}

public static function postos() {
    return DB::connection('sqlsrv')
        ->table('RODPOS')
        ->select('CODPON', 'DESCRI','CODCGC')
        ->orderBy('DESCRI')
        ->get();
}


public static function inserir_abastecimento($placa, $posto, $km, $motorista, $produto, $litragem, $valor, $id, $codaba) {
    
    // 1. Inicia uma transação no SQL Server
    DB::connection('sqlsrv')->beginTransaction();

    
    try{

    DB::connection('sqlsrv')
        ->table('RODABA')
        ->insert([
            'CODABA' => DB::raw('(SELECT ISNULL(MAX(CODABA), 0) + 1 FROM RODABA)'),
            'CODFIL' => 5,
            'NUMDOC' => $codaba . '-' . $produto,
            'ATUKMT' => $km,
            'CODPAD' => 1,
            'PLACA'  => $placa,
            'SITUAC' => 'I',
            'CODLIN' => 'RPURPU',
            'CODMOT' => $motorista,
            'CODPON' => $posto,
            'CODCMB' => $produto,
            'QUANTI' => $litragem,
            'VLRTOT' => $valor,
            'ULTKMT' => DB::raw("(SELECT ULTKMT FROM RODVEI WHERE CODVEI = '{$placa}')"),
            'CODCUS' => DB::raw("(SELECT CODCUS FROM RODVEI WHERE CODVEI = '{$placa}')"),
            'DATREF' => DB::raw('GETDATE()'),
            'DATINC' => DB::raw('GETDATE()'),
            'DATATU' => DB::raw('GETDATE()'),
            'USUATU' => 'IMPORTACAO',
            'USUINC' => 'IMPORTACAO',
            'OBSERV' => 'Importacao Pix'

        ]);

    DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

    } catch (\Exception $e) {
        DB::connection('sqlsrv')->rollBack();
        throw $e;
    }
}


public function abastecimentoBanRaz($id, $valor, $solicitante,$placa,$prazo, $codaba){


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



    // Agora sim, fazemos a lógica de padronização das contas
    $conta = '39020-5';
    



DB::connection('sqlsrv')->table('BANRAZ')->insert([
    'ID_RAZ' => DB::raw('(SELECT MAX(ID_RAZ) + 1 FROM BANRAZ)'),
    'NUMDOC' => $codaba,
    'CODCTA' => $conta,
    'TIPDOC' => 'TRA',
    'CODFIL' => $filial,
    'TIPORI' => 'TRA',
    'CODBCO' => 341,
    'CODHISBC' => 2,
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
    'OBSERV' => 'Formulário Pix ' . $id . ' - aprovado por '.$prazo,
    'COMPEN' => 'S',
    'CODTAR' => 11,
    'DATATU' => DB::raw('GETDATE()'),
    'USUATU' => 'IMPORTACAO',
    'USUINC' => 'IMPORTACAO',
    'CTATRA' => 'PROFROTAS', 
    'DATINC' => DB::raw('GETDATE()'),
    'CODCLIFOR' => NULL,
    'BLOQUE' => 'N',
    'NUMPED' => NULL,
    'SOLICI' => $solicitante,
    'VLRITX' => 1,
    'VLRITX_TRA' => 1,
    'CODTAX_TRA' => 1,
    'TIPTRA' => 'TRA',
    'FINALI' => $placa,

]);

DB::connection('sqlsrv')->table('BANRAT')->insert([
    'ID_BANRAT' => DB::raw('(SELECT MAX(ID_BANRAT) + 1 FROM BANRAT)'),
    'NUMDOC' => $id,
    'CODCTA' => $conta,
    'TIPDOC' => 'TRA',
    'CODFIL' => $filial,
    'CODUNN' => 19,
    'CODCGA' => 59,
    'CODCUS' => '102',
    'SINTET' => 171,
    'ANALIT' => 172,
    'VALOR' => $valor,
    'DATATU' => DB::raw('GETDATE()'),
    'USUATU' => 'IMPORTACAO',
    'DATINC' => DB::raw('GETDATE()'),
    'ID_RAZ' => DB::raw('(SELECT MAX(ID_RAZ) FROM BANRAZ)'),

]);


DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

} catch (\Exception $e) {
    DB::connection('sqlsrv')->rollBack();
    throw $e;
}
}





    public static function motoristasQuery($search = null)
    {
        try {
            $conn = DB::connection('sqlsrv');
            
            // 1. Evita que a consulta fique esperando travas de outras tabelas
            $query = $conn->table('RODMOT as M')
                ->lock('WITH (NOLOCK)') 
                ->select([
                    'CODMOT as codmot', 
                    'NOMMOT as nommot',
                    'NUMCPF as numcpf',

                ]);
            if ($search) {
                $query->where(function ($q) use ($search) {
                    // Se possível, remova o primeiro '%' para ganhar performance
                    $term = "%{$search}%"; 
                    $q->where('NOMMOT', 'LIKE', $term)
                    ->orWhere('CODMOT', 'LIKE', $term)
                    ->orWhere('NUMCPF', 'LIKE', $term);
                });
            }

            return $query->orderBy('CODMOT', 'desc')
                        ->limit(50)
                        ->get();

        } catch (\Exception $e) {
            // Logar o erro se necessário: Log::error($e->getMessage());
            return collect([]); // Retorna uma coleção vazia para não quebrar o front-end
        }
    }


}
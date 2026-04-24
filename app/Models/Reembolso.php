<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Reembolso extends Model
{
    use HasFactory;

        protected $table = 'reembolso';


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
        'ANALIT',
        'relatorio_id',
        'pix'
    ];

    // É bom definir o inverso da relação também
    public function relatorio()
    {
        return $this->belongsTo(Financeiro::class, 'relatorio_id');
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








public function pagrat($fornecedor, $valor, $id, $codunn, $codcus, $codgas, $produto, $sintet, $analit) {

    DB::connection('sqlsrv')->beginTransaction();

    try {
        // Convertemos para os tipos corretos
        $id = (string) $id;
        $valor = (float) $valor;
        $codunn = (int) $codunn;
        $codcus = (string) $codcus;
        $codgas = (int) $codgas;

        // 1. Procuramos se já existe um registro para este documento/rateio específico
        // Note que NÃO incluímos o VALOR no 'where', pois queremos achar o registro para SOMAR a ele.
        $registroId = DB::connection('sqlsrv')->table('PAGRAT')
            ->where('CODCLIFOR', $fornecedor)
            ->where('NUMDOC', $id)
            ->where('CODUNN', $codunn)
            ->where('CODCUS', $codcus)
            ->where('CODCGA', $codgas)
            ->where('SINTET',    $sintet) // Adicionado conforme sua regra
            ->where('ANALIT',    $analit) // Adicionado conforme sua regra
            ->value('ID_PAGRAT'); // Pega apenas o ID se existir

        if ($registroId) {
            // 2. Se existe, fazemos o UPDATE somando o valor novo ao atual
            DB::connection('sqlsrv')->table('PAGRAT')
                ->where('ID_PAGRAT', $registroId)
                ->update([
                    'VALOR'  => DB::raw("VALOR + $valor"), // Soma direto no SQL para evitar erro de precisão
                    'DATATU' => DB::raw('GETDATE()'),
                    'USUATU' => 'IMPORTACAO'
                ]);
        } else {
            // 3. Se não existe, fazemos o INSERT normal
            DB::connection('sqlsrv')->table('PAGRAT')->insert([
                'ID_PAGRAT' => DB::raw('(SELECT ISNULL(MAX(ID_PAGRAT), 0) + 1 FROM PAGRAT)'),
                'CODCLIFOR' => $fornecedor,
                'SERIE'     => 'A',
                'NUMDOC'    => $id,
                'CODUNN'    => $codunn,
                'CODCUS'    => $codcus,
                'CODCGA'    => $codgas,
                'SINTET'    => $sintet,
                'ANALIT'    => $analit,
                'VALOR'     => $valor,
                'USUATU'    => 'IMPORTACAO',
                'DATATU'    => DB::raw('GETDATE()'),
            ]);
        }

        DB::connection('sqlsrv')->commit();

    } catch (\Exception $e) {
        DB::connection('sqlsrv')->rollBack();
        throw $e;
    }
}


// public function pagrat($fornecedor,$valor,$id,$codunn,$codcus,$codgas,$produto,$sintet,$analit){


// // 1. Inicia uma transação no SQL Server
// DB::connection('sqlsrv')->beginTransaction();

// try{

//     $id = (string) $id;
//     $valor = (float) $valor;
//     $codunn = (int) $codunn;
//     $codcus = (string) $codcus;
//     $codgas = (int) $codgas;

//         // 🔎 Verifica duplicidade e ajusta valor
//     while (
//         DB::connection('sqlsrv')->table('PAGRAT')
//             ->where('CODCLIFOR', $fornecedor)
//             ->where('NUMDOC', $id)
//             ->where('VALOR', $valor)
//             ->exists()
//     ) {
//         $valor += 0.01; // soma 1 centavo até ficar único
//     }

//     DB::connection('sqlsrv')->table('PAGRAT')->insert([
//         'ID_PAGRAT' => DB::raw('(SELECT MAX(ID_PAGRAT) + 1 FROM PAGRAT)'),
//         'CODCLIFOR' => $fornecedor,
//         'SERIE' => 'A',
//         'NUMDOC' => $id,
//         'CODUNN' => $codunn,
//         'CODCUS' => $codcus,
//         'CODCGA' => $codgas,
//         'SINTET' => $sintet,
//         'ANALIT' => $analit,
//         'VALOR' => $valor,
//         'USUATU' => 'IMPORTACAO',
//         'DATATU' => DB::raw('GETDATE()'),
//     ]);

//     DB::connection('sqlsrv')->commit(); // Libera o cadeado para o próximo usuário

// } catch (\Exception $e) {
//     DB::connection('sqlsrv')->rollBack();
//     throw $e;
// }
// }





}

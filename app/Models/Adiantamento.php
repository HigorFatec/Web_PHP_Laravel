<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Adiantamento extends Model
{
    use HasFactory;
    protected $fillable = [
        'destino',
        'ida',
        'volta',
        'motivo',
        'validacao',
        'email_gestor',
        'observacoes',
        'nome',
        'cpf',
        'rg',
        'data_nascimento',
        'email',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'titular',
        'pix',
        'filial_viajante',
        'user_name',
        'user_id',
        'user_cpf',
        'user_email',
        
        'approval_token',
        'anexo_path',
        'status',
        'fornecedor',
        'valor',
        'gestor_aprovador',
        'cod_gasto',
        'cod_custo',
        'cod_unidade',
        'tipo_pix',
        'id_raz',
        'situac',
        'valor_utilizado',
        'valor_liquido'

        ];

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



        public static function fornecedoresQuery($search = null)
        {
            $query = DB::connection('sqlsrv')
                ->table('RODCLI')
                ->select(
                    'CODCLIFOR as codclifor', 
                    'RAZSOC as razsoc',
                    DB::raw('ISNULL(CODCGC, CODCPF) as cnpj'),
                    DB::raw('ISNULL(BANDEP, \'\') as banco'),
                    DB::raw('ISNULL(NUMAGE, \'\') as agencia'),
                    DB::raw('ISNULL(CONTAC, \'\') as conta'),
                    DB::raw('ISNULL(NOMFAV, \'\') as favorecido'),
                    DB::raw('ISNULL(INSCRI, \'\') as rg'),
                );

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('RAZSOC', 'LIKE', "%{$search}%")
                    ->where('CLASSI','5')
                    ->orWhere('CODCPF', 'LIKE', "%{$search}%")
                    ->where('CLASSI','5')
                    ->orWhere('CODCGC', 'LIKE', "%{$search}%")
                    ->where('CLASSI','5')
                    ->orWhere('CODCLIFOR', 'LIKE', "%{$search}%")
                    ->where('CLASSI','5');
                });
            }

            return $query->orderBy('CODCLIFOR', 'desc')
                        ->limit(50) // deixar leve
                        ->get();
        }





        public function financeiroAvista($id, $valor, $solicitante, $fornecedor ,$prazo, $filial, $conta, $codunn, $codcus, $codgas){

        $id = (int) $id;
        $valor = (float) $valor;
        $codunn = (int) $codunn;
        $codgas = (int) $codgas;
        $codcus = (string) $codcus;

        $saldo_anterior = DB::connection('sqlsrv')
            ->table('BANRAZ')
            ->orderByDesc('ID_RAZ')
            ->value('SLDATU');  // pega somente o valor

        $saldo_anterior = (float) $saldo_anterior;

        $saldo_atualizado = $saldo_anterior - (float) $valor;

        $filial = 5;



        DB::connection('sqlsrv')->table('BANRAZ')->insert([
            'ID_RAZ' => DB::raw('(SELECT MAX(ID_RAZ) + 1 FROM BANRAZ)'),
            'NUMDOC' => $id,
            'CODCTA' => $conta,
            'TIPDOC' => 'ADV',
            'CODFIL' => $filial,
            'TIPORI' => 'ADV',
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
            'OBSERV' => 'Adiantamento de Viagem - Formulário Adiantamento - aprovado por '.$prazo,
            'COMPEN' => 'N',
            'CODTAR' => 1687,
            'DATATU' => DB::raw('GETDATE()'),
            'USUATU' => 'IMPORTACAO',
            'USUINC' => 'IMPORTACAO',
            'CTATRA' => NULL, 
            'DATINC' => DB::raw('GETDATE()'),
            'CODCLIFOR' => $fornecedor,
            'BLOQUE' => 'N',
            'NUMPED' => NULL,
            'SOLICI' => $solicitante,
            'VLRITX' => 0,
            'VLRITX_TRA' => 0,
            'FINALI' => NULL,

        ]);

        DB::connection('sqlsrv')->table('BANRAT')->insert([
            'ID_BANRAT' => DB::raw('(SELECT MAX(ID_BANRAT) + 1 FROM BANRAT)'),
            'NUMDOC' => $id,
            'CODCTA' => $conta,
            'TIPDOC' => 'ADV',
            'CODFIL' => $filial,
            'CODUNN' => $codunn,
            'CODCGA' => $codgas,
            'CODCUS' => $codcus,
            'SINTET' => 494,
            'ANALIT' => 53,
            'VALOR' => $valor,
            'DATATU' => DB::raw('GETDATE()'),
            'USUATU' => 'IMPORTACAO',
            'DATINC' => DB::raw('GETDATE()'),
            'ID_RAZ' => DB::raw('(SELECT MAX(ID_RAZ) FROM BANRAZ)'),

        ]);

        DB::connection('sqlsrv')->table('BANRAZ')
            ->where('ID_RAZ', DB::raw('(SELECT MAX(ID_RAZ) FROM BANRAZ)'))
            ->update([
            'SITUAC' => 'I',
        ]);

        }







}
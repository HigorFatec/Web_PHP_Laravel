<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GestoresSaldo;

class ImplantacaoSaldo extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo_lote',
        'user_id',
        'cod_localizacao',
        'descricao_localizacao',
        'movimentacao',
        'status',
        'observacao',
        'posicao',
        'produto',
        'descricao_produto',
        'grupo',
        'subgrupo',
        'quantidade',
        'saldo_fisico',
        'valor_medio',
        'valor_total',
        'gestor_filial',
        'gestor_regional',
        'diretor',
        'aprovacao_filial',
        'aprovacao_regional',
        'aprovacao_diretoria'
    ];


    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

    // Relacionamento com o gestor
    public function diretores()
    {
        return $this->belongsTo(GestoresSaldo::class, 'diretor', 'email'); 
        // 'email_gestor' é a coluna em fiscais
        // 'email' é a coluna correspondente em gestores_aprovadores
    }

        // Relacionamento com o gestor
    public function regional()
    {
        return $this->belongsTo(GestoresSaldo::class, 'gestor_regional', 'email'); 
        // 'email_gestor' é a coluna em fiscais
        // 'email' é a coluna correspondente em gestores_aprovadores
    }
        // Relacionamento com o gestor
    public function local()
    {
        return $this->belongsTo(GestoresSaldo::class, 'gestor_filial', 'email'); 
        // 'email_gestor' é a coluna em fiscais
        // 'email' é a coluna correspondente em gestores_aprovadores
    }

// Campo Saldo físico: Trazer da aba histórico do módulo cadastro de produto (campo saldo físico, da última data do código da localização)

public static function produtos(){
    return DB::connection('sqlsrv')
        ->table('ESTPRO AS PRO')
        ->join('ESTGRP AS GRP', 'PRO.CODGPP', '=', 'GRP.CODGPP')
        ->join('ESTSGP AS SGP', 'PRO.CODSGP', '=', 'SGP.CODSGP')
        ->select(
            'PRO.CODPROD',
            'PRO.DESCRI',
            'PRO.CODGPP',
            'PRO.CODSGP',
            'GRP.DESCRI AS DESCRIGPP',
            'SGP.DESCRI AS DESCRISGP'
        )
        ->where('PRO.TIPPRO','=','P')
        ->get();
}

public static function filial () {
    return DB::connection('sqlsrv')
        ->table('ESTLOC')
        ->select(
            'CODIGO',
            'DESCRI'
        )
        ->where('LOCDES', '=',  'N')
        ->whereIn('CODIGO', [21,22,20,25,26,51])
        ->get();
}

public static function localizacoes($codloc, $codprod){
    return DB::connection('sqlsrv')
        ->table('ESTPRL AS PRL')
        ->leftjoin('ESTPRO AS PRO', 'PRL.CODPROD', '=', 'PRO.CODPROD')
        ->leftjoin('ESTLPR AS LPR', 'PRL.CODPROD', '=', 'LPR.CODPROD')
        ->select(
            'PRL.CODLOC',
            'PRL.SALFIS',
            DB::raw('DBO.F_EST_DESCRI_LOC(PRL.CODLOC) AS LOCALIZACAO'),
            DB::raw('ISNULL(LPR.POSICA,PRO.LOCALI) AS POSICAO')
        )
        ->where('PRL.CODLOC', $codloc)
        ->where('PRL.CODPROD', $codprod)
        ->first();
}



public static function produtosQuery($search = null)
{
    try {
        $conn = DB::connection('sqlsrv');
        
        // 1. Evita que a consulta fique esperando travas de outras tabelas
        $query = $conn->table('ESTPRO as PRO')
            ->lock('WITH (NOLOCK)') 
            ->join('ESTGRP as GCP', 'PRO.CODGPP', '=', 'GCP.CODGPP')
            ->join('ESTSGP as SGP', 'PRO.CODSGP', '=', 'SGP.CODSGP')
            ->select([
                $conn->raw('CODPROD as codprod'), 
                $conn->raw('PRO.DESCRI as descri'),
                $conn->raw('GCP.CODGPP'),
                $conn->raw('SGP.CODSGP'),
                $conn->raw('GCP.DESCRI as DESCRIGPP'),
                $conn->raw('SGP.DESCRI as DESCRISGP'),
                $conn->raw('PREMED'),


            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Se possível, remova o primeiro '%' para ganhar performance
                $term = "%{$search}%"; 
                $q->where('CODPROD', 'LIKE', $term)
                ->orWhere('PRO.DESCRI', 'LIKE', $term);
            });
        }

        return $query->orderBy('PRO.DESCRI', 'desc')
                    ->limit(50)
                    ->get();

    } catch (\Exception $e) {
        // Logar o erro se necessário: Log::error($e->getMessage());
        return collect([]); // Retorna uma coleção vazia para não quebrar o front-end
    }
}
}
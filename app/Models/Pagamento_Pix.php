<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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
        'status'
    ];

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


public static function inserir_abastecimento($placa, $km, $motorista, $produto, $litragem, $valor) {
    return DB::connection('sqlsrv')
        ->table('RODABA')
        ->insert([
            'CODABA' => DB::raw('(SELECT ISNULL(MAX(CODABA), 0) + 1 FROM RODABA)'),
            'CODFIL' => 5,
            'ATUKMT' => $km,
            'CODPAD' => 1,
            'SITUAC' => 'I',
            'CODLIN' => 'RPURPU',
            'CODMOT' => $motorista,
            'CODCMB' => $produto,
            'QUANTI' => $litragem,
            'VLRTOT' => $valor,
            'DATREF' => DB::raw('GETDATE()'),
            'DATINC' => DB::raw('GETDATE()'),
            'DATATU' => DB::raw('GETDATE()'),
            'USUATU' => 'IMPORTACAO',
            'USUINC' => 'IMPORTACAO',

        ]);

}
}
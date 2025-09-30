<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OS extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';
    protected $table = 'OSEORD';
    protected $primaryKey = 'CODORD';
    public $timestamps = false;

public static function getByMatriculaQuery($codfun)
{
    return DB::connection('sqlsrv')
        ->table('OSEORD AS O')
        ->join('OSEIOS AS IOS', 'O.CODORD', '=', 'IOS.CODORD')
        ->join('RODIRE AS IRE', 'IOS.CODIRE', '=', 'IRE.CODIRE')
        ->join('RODVEI AS VEI', 'O.CODVEI', '=', 'VEI.CODVEI')
        ->select(
            'O.CODORD AS ORDEM',
            'O.CODFIL AS FILIAL',
            'O.CODFUN AS MATRICULA',
            'O.CODVEI AS PLACA',
            'O.DATREF AS CRIADO',
            'O.PREVEN AS PREVISAO',
            'O.AUTORI AS AUTORIZADO',
            'IOS.CODIRE AS Cod_Item',
            'IOS.SEQUEN AS SEQUENCIA',
            'IRE.DESCRI AS Item_Revisional',
            'IOS.OBSERV AS MANUTENCAO',
            'IOS.USUINC AS Criado_por',
            'VEI.CODUNN AS Unidade',
            'IOS.SITUAC AS Situacao_Item'
        )
        //->where('O.CODFUN', $codfun)
        ->where('O.TIPORD', '1')
        ->where('O.SITUAC', '!=', 'O')
        ->where('O.TIPMAN' , 'V')
        ->where('O.AUTORI', 'S')
        ->where('VEI.CODUNN', '57')
        ->where('IOS.SITUAC', '!=', 'C');
}

public static function getByOrdemSequencia($codord, $sequen)
{
    return DB::connection('sqlsrv')
        ->table('OSEORD AS O')
        ->join('OSEIOS AS IOS', 'O.CODORD', '=', 'IOS.CODORD')
        ->join('RODIRE AS IRE', 'IOS.CODIRE', '=', 'IRE.CODIRE')
        ->join('RODVEI AS VEI', 'O.CODVEI', '=', 'VEI.CODVEI')
        ->select(
            'O.CODORD AS ORDEM',
            'O.CODFIL AS FILIAL',
            'O.CODFUN AS MATRICULA',
            'O.CODVEI AS PLACA',
            'O.DATREF AS CRIADO',
            'O.PREVEN AS PREVISAO',
            'O.AUTORI AS AUTORIZADO',
            'IOS.CODIRE AS Cod_Item',
            'IOS.SEQUEN AS SEQUENCIA',
            'IRE.DESCRI AS Item_Revisional',
            'IOS.OBSERV AS MANUTENCAO',
            'IOS.USUINC AS Criado_por',
            'VEI.CODUNN AS Unidade',
            'IOS.SITUAC AS Situacao_Item'       
        )
        ->where('O.CODORD', $codord)
        ->where('IOS.SEQUEN', $sequen);
}

}

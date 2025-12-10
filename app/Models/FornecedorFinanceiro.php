<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FornecedorFinanceiro extends Model
{
    protected $table = 'fornecedor_adiantamentos';
    protected $fillable = [
        'tipo',
        'nome_remetente',
        'email_remetente',
        'razao_social',
        'nome_abreviado',
        'cpf',
        'cnpj',
        'ie',
        'rg',
        'cidade',
        'endereco',
        'bairro',
        'email',
        'banco',
        'agencia',
        'conta',
        'favorecido',
        'pix_aleatorio',
        'approval_token',
        'status',
        'email_fornecedor'

    ];

public static function cidades(){
    return DB::connection('sqlsrv')
        ->table('RODMUN')
        ->select(
            'CODMUN',
            'DESCRI'
        )
        ->get();
}

public static function fornecedoresQuery()
{
    return DB::connection('sqlsrv')
        ->table('RODCLI')
        ->select('CODCLIFOR as codclifor', 
                 'RAZSOC as razsoc',
                 DB::raw('ISNULL(CODCGC, CODCPF) as cnpj'),
                 DB::raw('ISNULL(BANDEP, \'\') as banco'),
                 DB::raw('ISNULL(NUMAGE, \'\') as agencia'),
                 DB::raw('ISNULL(CONTAC, \'\') as conta'),
                 DB::raw('ISNULL(NOMFAV, \'\') as favorecido'),
                 DB::raw('ISNULL(CHVALE, \'\') as pix_aleatorio')

                 
        )
        ->orderBy('CODCLIFOR', 'desc')
        ->limit(20000)
        ->get();
}



public static function cadastro_Fornecedor_juridico($razao_social,$nome_abreviado,$endereco,$bairro,$cod_municipio,$ie,$cnpj,$banco,$agencia,$conta,$favorecido,$pix_aleatorio,$email_fornecedor)
{
    $nome_abreviado = (string) $nome_abreviado;
    $endereco = (string) $endereco;
    $bairro = (string) $bairro;
    $cnpj = (string) $cnpj;
    $ie = (string) $ie;
    $cod_municipio = (int) $cod_municipio;
    $razao_social = (string) $razao_social;
    $banco = (int) $banco;
    $agencia = (int) $agencia;
    $conta = (int) $conta;
    $favorecido = (string) $favorecido;

    $cnpj = preg_replace('/\D/', '', $cnpj);
    $cnpj = substr($cnpj, 0, 2) . '.' .
            substr($cnpj, 2, 3) . '.' .
            substr($cnpj, 5, 3) . '/' .
            substr($cnpj, 8, 4) . '-' .
            substr($cnpj, 12, 2);



// INSERT INTO RODCLI (CODCLIFOR, RAZSOC, CODMUN, CODFIL, DATATU, USUATU, CODPAD)
// VALUES ('173853', 'NOME TESTE', '4174', '5', GETDATE(), 'USUARIO_TESTE', '1');

DB::connection('sqlsrv')->table('RODCTC')->insert([
    'USUATU' => 'ODAHCAM',
    'ID' => DB::raw('(SELECT MAX(ID) + 1 FROM RODCTC)'),
    'CODCLIFOR' => DB::raw('(SELECT MAX(CODCLIFOR) + 1 FROM RODCLI)'),
    'DATATU' => DB::raw('GETDATE()'),
    'CODTIP' => 3,
    'EMAIL' => $email_fornecedor,
    'NOMECT' => $nome_abreviado
]);


DB::connection('sqlsrv')->table('RODCLI')->insert([
    'USUATU' => 'ODAHCAM',
    'USUINC' => 'ODAHCAM',
    'DATINC' => DB::raw('GETDATE()'),
    'SITUAC' => 'A',
    'NOMFAV' => $razao_social,
    'FISJUR' => 'J',
    'CODCGC' => $cnpj,
    'JURISD' => 'U',
    'SITFIS' => 2,
    'RAZSOC' => $razao_social,
    'CODCLIFOR' => DB::raw('(SELECT MAX(codclifor) + 1 FROM rodcli)'),
    'CODFIL' => 5,
    'NOMEAB' => $nome_abreviado,
    'ENDERE' => $endereco,
    'BAIRRO' => $bairro,
    'CODMUN' => $cod_municipio,
    'PAISLO' => 'BRASIL',
    'INSCRI' => $ie,
    'CLASSI' => 2,
    'CODPAD' => 1,
    'SITFIS' => 2,
    'QUALIF' => 3,
    'DATATU' => DB::raw('GETDATE()'),
    'BANDEP' => $banco,
    'NUMAGE' => $agencia,
    'CONTAC' => $conta,
    'NOMFAV' => $favorecido,
    'CHVALE' => $pix_aleatorio
]);

}

public static function verificar_cpf($cpf){
    // TRATA CPF
    $cpf = preg_replace('/\D/', '', $cpf); // remove espaço

    if (strlen($cpf) === 11) {
        $cpf = substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    $cpf = substr($cpf, 0, 14); // garante que não estoura varchar(14)

    $cpfExiste = DB::connection('sqlsrv')
    ->table('RODCLI')
    ->where('CODCPF', $cpf)
    ->exists();

    if ($cpfExiste) {
        return [
            'success' => false,
            'message' => 'CPF já cadastrado no sistema.'
        ];
    }
}

public static function verificar_cnpj($cnpj){
        $cnpj = preg_replace('/\D/', '', $cnpj);
    $cnpj = substr($cnpj, 0, 2) . '.' .
            substr($cnpj, 2, 3) . '.' .
            substr($cnpj, 5, 3) . '/' .
            substr($cnpj, 8, 4) . '-' .
            substr($cnpj, 12, 2);

    $cnpjExiste = DB::connection('sqlsrv')
    ->table('RODCLI')
    ->where('CODCGC', $cnpj)
    ->exists();

    if ($cnpjExiste) {
        return [
            'success' => false,
            'message' => 'CNPJ já cadastrado no sistema.'
        ];
    }
}

public static function cadastro_Fornecedor_fisico($razao_social,$nome_abreviado,$endereco,$bairro,$cod_municipio,$rg,$cpf,$banco,$agencia,$conta,$favorecido,$pix_aleatorio,$email_fornecedor)
{  
    $razao_social = (string) $razao_social;
    $nome_abreviado = (string) $nome_abreviado;
    $endereco = (string) $endereco;
    $bairro = (string) $bairro;
    $rg = (int) $rg;
    $cpf = (string) $cpf;
    $cod_municipio = (int) $cod_municipio;
    $banco = (int) $banco;
    $agencia = (int) $agencia;
    $conta = (int) $conta;
    $favorecido = (string) $favorecido;

    // TRATA RG
    $rg = preg_replace('/\D/', '', $rg);

    // TRATA CPF
    $cpf = preg_replace('/\D/', '', $cpf); // remove espaço

    if (strlen($cpf) === 11) {
        $cpf = substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    $cpf = substr($cpf, 0, 14); // garante que não estoura varchar(14)
    

    // dd('CPF formatado antes do insert:', $cpf, strlen($cpf));

DB::connection('sqlsrv')->table('RODCTC')->insert([
    'USUATU' => 'ODAHCAM',
    'ID' => DB::raw('(SELECT MAX(ID) + 1 FROM RODCTC)'),
    'CODCLIFOR' => DB::raw('(SELECT MAX(CODCLIFOR) + 1 FROM RODCLI)'),
    'DATATU' => DB::raw('GETDATE()'),
    'CODTIP' => 3,
    'EMAIL' => $email_fornecedor,
    'NOMECT' => $nome_abreviado
]);


DB::connection('sqlsrv')->table('RODCLI')->insert([
    'USUATU' => 'ODAHCAM',
    'USUINC' => 'ODAHCAM',
    'DATINC' => DB::raw('GETDATE()'),
    'RAZSOC' => $razao_social,
    'FISJUR' => 'F',
    'CODCLIFOR' => DB::raw('(SELECT MAX(CODCLIFOR) + 1 FROM RODCLI)'),
    'CODFIL' => 5,
    'NOMEAB' => $nome_abreviado,
    'ENDERE' => $endereco,
    'BAIRRO' => $bairro,
    'CODMUN' => $cod_municipio,
    'INSCRI' => $rg,
    'CODCPF' => $cpf,
    'PAISLO' => 'BRASIL',
    'CLASSI' => 2,
    'CODPAD' => 1,
    'SITFIS' => 6,
    'SITUAC' => 'A',
    'QUALIF' => 3,
    'DATATU' => DB::raw('GETDATE()'),
    'BANDEP' => $banco,
    'NUMAGE' => $agencia,
    'CONTAC' => $conta,
    'NOMFAV' => $favorecido,
    'CHVALE' => $pix_aleatorio
]);
}







}
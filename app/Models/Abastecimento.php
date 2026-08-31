<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Abastecimento extends Model
{
    public static function inserir_em_massa(array $abastecimentos, int $userId, string $nomeArquivo) {
        
        // 1. Abre a transação no SQL Server
        DB::connection('sqlsrv')->beginTransaction();
        
        // 2. Abre a transação no MySQL 
        DB::connection('mysql')->beginTransaction();

        try {
            $ultimoCodAba = DB::connection('sqlsrv')->table('RODABA')->max('CODABA') ?? 0;

            foreach ($abastecimentos as $item) {
                $ultimoCodAba++;
                $placa = str_replace("'", "''", $item['placa']);

                // --- CONVERSÃO DE DATA BRASILEIRA (dd/mm/aaaa hh:mm) PARA SQL SERVER ---
                try {
                    if (!empty($item['datref'])) {
                        // Converte o formato do seu CSV "23/06/2026 19:20" para o padrão do banco
                        $datrefFormatted = Carbon::createFromFormat('d/m/Y H:i', $item['datref'])->format('Y-m-d H:i:s');
                    } else {
                        $datrefFormatted = DB::raw('GETDATE()');
                    }
                } catch (\Exception $e) {
                    // Caso alguma linha venha vazia ou fora do padrão, não quebra o sistema
                    $datrefFormatted = DB::raw('GETDATE()');
                }

                DB::connection('sqlsrv')
                    ->table('RODABA')
                    ->insert([
                        'CODABA' => $ultimoCodAba,
                        'CODFIL' => 5,
                        'NUMDOC' => $item['codaba'] . '-' . $item['produto'],
                        'ATUKMT' => $item['km'],
                        'CODPAD' => 1,
                        'PLACA'  => $item['placa'],
                        'SITUAC' => 'O',
                        'CODLIN' => 'RPURPU',
                        'CODMOT' => $item['motorista'],
                        'CODPON' => $item['posto'],
                        'CODCMB' => $item['produto'],
                        'QUANTI' => $item['litragem'], 
                        'VLRTOT' => $item['valor'],    
                        'ULTKMT' => DB::raw("(SELECT ULTKMT FROM RODVEI WHERE CODVEI = '{$placa}')"),
                        'CODCUS' => DB::raw("(SELECT CODCUS FROM RODVEI WHERE CODVEI = '{$placa}')"),
                        'DATREF' => $datrefFormatted,  
                        'DATINC' => DB::raw('GETDATE()'),
                        'DATATU' => DB::raw('GETDATE()'),
                        'USUATU' => 'IMPORTACAO',
                        'USUINC' => 'IMPORTACAO',
                        'OBSERV' => 'Importacao Pix'
                    ]);
            }

            // 3. Salva o Histórico original no MySQL sem novos campos adicionais
            DB::connection('mysql')->table('historico_importacoes')->insert([
                'user_id'         => $userId,
                'nome_arquivo'    => $nomeArquivo,
                'total_registros' => count($abastecimentos),
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            // Confirma operações em ambos os bancos
            DB::connection('sqlsrv')->commit();
            DB::connection('mysql')->commit();

        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            DB::connection('mysql')->rollBack();
            throw $e;
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Abastecimento;

class AbastecimentoController extends Controller
{
    /**
     * Exibe a tela com o formulário de importação.
     */
    public function index()
    {
        // Altere para o caminho correto onde você salvou a view.
        // Se salvou em resources/views/abastecimento/importar.blade.php:
        return view('abastecimento.importar'); 
    }

/**
 * Processa o arquivo CSV enviado, permitindo o prosseguimento 
 * mesmo que linhas individuais apresentem erro e gerando relatório dos inseridos.
 */
public function importar(Request $request)
{
    $user = auth()?->user();

    if (!$user) {
        return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
    }

    if ($user->temSetor(['frotas','admin'])){
        // Permitir acesso
    } else {
        return response()->json(['sucesso' => false, 'message' => 'Acesso negado.'], 403);
    }

    $request->validate([
        'arquivo' => 'required|file|mimes:csv,txt|max:10240',
    ]);

    if (!$request->hasFile('arquivo') || !$request->file('arquivo')->isValid()) {
        return redirect()->back()->withErrors(['error' => 'Falha no envio do arquivo. Verifique o tamanho ou se o arquivo está corrompido.']);
    }

    try {
        $path = $request->file('arquivo')->getRealPath();

        if (empty($path) || ($handle = fopen($path, 'r')) === false) {
            return redirect()->back()->withErrors(['error' => 'Não foi possível ler o arquivo enviado.']);
        }
        
        $header = fgetcsv($handle, 1000, ';'); 
        
        if (!$header) {
            fclose($handle);
            return redirect()->back()->withErrors(['error' => 'O arquivo CSV está vazio ou ilegível.']);
        }

        $header = array_map(function($item) {
            $item = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $item));
            return str_replace('"', '', $item); 
        }, $header);

        $cabecalhosObrigatorios = [
            'CODCON', 'NUMDOC', 'Data/Hora do Abastecimento', 
            'Placa Cavalo Trator', 'COMBUSTIVEL', 'Quilometragem', 
            'Quantidade de Litros', 'VALOR', 'CODMOT'
        ];

        $diff = array_diff($cabecalhosObrigatorios, $header);

        if (!empty($diff)) {
            fclose($handle);
            $colunasFaltantes = implode(', ', $diff);
            return redirect()->back()->withErrors([
                'error' => "Estrutura do CSV inválida! Colunas obrigatórias ausentes: [{$colunasFaltantes}]."
            ]);
        }

        $userId = auth()->id() ?? 1;
        $nomeArquivo = $request->file('arquivo')->getClientOriginalName();

        $numeroLinha = 1;
        $sucessos = 0;
        $erros = [];
        $dadosInseridosSucesso = []; // Guarda as linhas inseridas com sucesso

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            $numeroLinha++;

            if (count($row) < count($header)) {
                $erros[] = "Linha {$numeroLinha}: Quantidade de colunas insuficiente.";
                continue;
            }

            try {
                $linha = array_combine($header, $row);

                // --- TRATAMENTO DO VALOR ---
                $valorLimpo = $linha['VALOR'] ?? '0';
                $valorLimpo = str_replace(['R$', ' ', '.'], '', $valorLimpo);
                $valorLimpo = str_replace(',', '.', $valorLimpo);
                $valor = floatval($valorLimpo);

                // --- TRATAMENTO DA LITRAGEM ---
                $litragemLimpo = $linha['Quantidade de Litros'] ?? '0';
                $litragemLimpo = str_replace([' ', '.'], '', $litragemLimpo);
                $litragemLimpo = str_replace(',', '.', $litragemLimpo);
                $litragem = floatval($litragemLimpo);

                $dadosLinha = [
                    'placa'     => trim($linha['Placa Cavalo Trator'] ?? ''),
                    'posto'     => intval($linha['CODCON'] ?? 0),
                    'km'        => intval($linha['Quilometragem'] ?? 0),
                    'motorista' => intval($linha['CODMOT'] ?? 0),
                    'produto'   => intval($linha['COMBUSTIVEL'] ?? 0),
                    'litragem'  => $litragem, 
                    'valor'     => $valor,    
                    'codaba'    => trim($linha['NUMDOC'] ?? ''),
                    'datref'    => trim($linha['Data/Hora do Abastecimento'] ?? ''),
                ];

                // Executa a inserção no banco
                Abastecimento::inserir_em_massa([$dadosLinha], $userId, $nomeArquivo);
                
                $sucessos++;
                $dadosInseridosSucesso[] = $dadosLinha; // Registra para a planilha de saída

            } catch (\Exception $e) {
                $mensagemErro = $e->getMessage();

                // Limpa mensagens do SQL Server mantendo a causa real
                $mensagemErro = preg_replace('/^SQLSTATE\[\w+\]:\s*(?:\[.*?\])+/', '', $mensagemErro);
                if ($posSql = strpos($mensagemErro, '(Connection:')) {
                    $mensagemErro = trim(substr($mensagemErro, 0, $posSql));
                }

                $erros[] = "Linha {$numeroLinha}: " . trim($mensagemErro);
            }
        }

        fclose($handle);

        $redirect = redirect()->back();

        // Se houver inserções com sucesso, gera o CSV do relatório
        if ($sucessos > 0) {
            $nomeRelatorio = 'importados_sucesso_' . date('Ymd_His') . '.csv';
            $diretorio = storage_path('app/public/relatorios_importacao');

            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            $caminhoArquivo = $diretorio . '/' . $nomeRelatorio;
            $fp = fopen($caminhoArquivo, 'w');

            // Grava o cabeçalho no arquivo
            fputcsv($fp, array_keys($dadosInseridosSucesso[0]), ';');

            // Grava os dados inseridos
            foreach ($dadosInseridosSucesso as $linhaInserida) {
                fputcsv($fp, $linhaInserida, ';');
            }
            fclose($fp);

            // Gera a URL para download
            $downloadUrl = asset('storage/relatorios_importacao/' . $nomeRelatorio);

            $redirect->with('success', "{$sucessos} abastecimentos importados com sucesso!")
                     ->with('download_url', $downloadUrl);
        }

        if (!empty($erros)) {
            $redirect->withErrors($erros);
        }

        if ($sucessos === 0 && empty($erros)) {
            return redirect()->back()->withErrors(['error' => 'Nenhum dado válido encontrado para inserção.']);
        }

        return $redirect;

    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Falha crítica na importação: ' . $e->getMessage()]);
    }
}


public function exemploCsv()
{
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="exemplo_abastecimento.csv"',
    ];

    // Cabeçalhos exatos do seu modelo de importação
    $cabecalhos = [
        'CODCON', 
        'NUMDOC', 
        'Data/Hora do Abastecimento', 
        'Placa Cavalo Trator', 
        'COMBUSTIVEL', 
        'Quilometragem', 
        'Quantidade de Litros', 
        'VALOR', 
        'CODMOT'
    ];

    // Uma linha de dados fictícios para o utilizador entender o formato
    $linhaExemplo = [
        '350',
        '5068a',
        '30/06/2026  19:07:00',
        'RUB0H59',
        '11',
        '280137',
        '40',
        '111,6',
        '112431'
    ];

    $callback = function() use ($cabecalhos, $linhaExemplo) {
        $file = fopen('php://output', 'w');
        
        // Insere o BOM para o Excel abrir corretamente em PT-BR com acentos e UTF-8
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Escreve os cabeçalhos e a linha separando por ponto e vírgula ';'
        fputcsv($file, $cabecalhos, ';');
        fputcsv($file, $linhaExemplo, ';');
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
    
}
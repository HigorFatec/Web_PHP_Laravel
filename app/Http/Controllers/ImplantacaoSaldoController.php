<?php

namespace App\Http\Controllers;

use App\Models\ImplantacaoSaldo;
use App\Models\GestoresSaldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Mail;

class ImplantacaoSaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $diretor = GestoresSaldo::where('tipo_gestor','=','Diretor')->orderBy('id')->get();
        $regional = GestoresSaldo::where('tipo_gestor','=','Regional')->orderBy('id')->get();
        $local = GestoresSaldo::where('tipo_gestor','=','Local')->orderBy('id')->get();

        $produtos = ImplantacaoSaldo::produtos();
        $filiais = ImplantacaoSaldo::filial();

        $itensTemporarios = DB::table('itens_temporarios_lote')
            ->where('user_id', Auth::id())
            ->get();

        $fornecedores = []; 
    
        return view('implantacao_saldo.index', compact('produtos','diretor','regional','local','filiais','itensTemporarios'));
    }

    public function buscarProdutos(Request $request)
    {
        $search = $request->search;
        $produtos = ImplantacaoSaldo::produtosQuery($search);
        return response()->json($produtos);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function menu()
    {
        return view('implantacao_saldo.menu');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cod_localizacao'   => 'required',
            'observacao'        => 'required',
            'gestor_filial'     => 'required',
            'gestor_regional'   => 'required',
            'diretor'           => 'required',
            'produtos'          => 'required|array|min:1', 
        ]);

        $user = Auth::user();
        $produtosEnviados = $request->input('produtos');
        
        $codigoLote = 'INV-' . strtoupper(uniqid());

        // 1. Tratamento rigoroso da localização (Remove espaços e garante que se for numérico, seja tratado corretamente)
        $codlocGeral = trim($request->cod_localizacao);

        $descricao_localizacao = DB::connection('sqlsrv')
            ->table('ESTLOC')
            ->select('DESCRI')
            ->where('CODIGO', $codlocGeral)
            ->first();

        foreach ($produtosEnviados as $item) {
            // 2. Limpeza rigorosa do código do produto
            $codprod = trim($item['codprod']);
            $quantidade = (float) $item['quantidade'];
            
            // 🌟 CAPTURA DIRETO DO INPUT DA TELA: Se o usuário alterou no select da tabela, você pega o valor real
            $movimentacaoItem = isset($item['movimentacao']) ? $item['movimentacao'] : 'Entrada';

            // Recupera grupo e subgrupo do rascunho temporário local
            $dadosProdTemporario = DB::table('itens_temporarios_lote')
                ->where('user_id', $user->id)
                ->where('codprod', $codprod)
                ->first();

            // 3.1 Busca o Preço Médio diretamente da tabela ESTPRO
            $produtoEstpro = DB::connection('sqlsrv')
                ->table('ESTPRO')
                ->select('PREMED')
                ->where('CODPROD', $codprod)
                ->first();

            $valorMedio = $produtoEstpro ? (float) $produtoEstpro->PREMED : 0.0;
            // Agora sim: Valor Total = Quantidade multiplicada pelo Valor Médio
            $valorTotal = $quantidade * $valorMedio;

            // 3.2 Busca o Saldo Físico na tabela ESTPRL
            $saldoFisico = DB::connection('sqlsrv')
                ->table('ESTPRL AS PRL')
                ->select('SALFIS')
                ->where('CODPROD', $codprod)
                ->where('CODLOC', $codlocGeral)
                ->first();

            // 4. Consulta de Posição usando os mesmos parâmetros limpos
            $posicao = DB::connection('sqlsrv')
                ->table('ESTLPR')
                ->select('POSICA')
                ->where('CODPROD', $codprod)
                ->where('CODLOC', $codlocGeral)
                ->first();

            // 🛠️ DEBUG TEMPORÁRIO (Caso continue vindo zerado, descomente as linhas abaixo para ver o que a query está recebendo)
            
            // if (is_null($saldoFisico) || is_null($posicao)) {
            //     dd([
            //         'Erro' => 'A query não encontrou o produto no SQL Server',
            //         'Produto_Buscado' => $codprod,
            //         'Localizacao_Buscada' => $codlocGeral,
            //         'Retorno_Saldo_Objeto' => $saldoFisico,
            //         'Retorno_Posicao_Objeto' => $posicao,
            //         'Valor Medio' => $valorMedio,
            //         'Valor Total' => $valorTotal
            //     ]);
            // }
            

            ImplantacaoSaldo::create([
                'user_id'               => $user->id,
                'codigo_lote'           => $codigoLote, 
                'cod_localizacao'       => $request->cod_localizacao,
                'descricao_localizacao' => $descricao_localizacao ? $descricao_localizacao->DESCRI : null,

                'movimentacao'          => $movimentacaoItem,

                'observacao'            => $request->observacao,
                'gestor_filial'         => $request->gestor_filial,
                'gestor_regional'       => $request->gestor_regional,
                'diretor'               => $request->diretor,
                'produto'               => $codprod,
                'descricao_produto'     => $dadosProdTemporario ? $dadosProdTemporario->descricao : 'N/A',
                'grupo'                 => $dadosProdTemporario ? $dadosProdTemporario->grupo : 0,
                'subgrupo'              => $dadosProdTemporario ? $dadosProdTemporario->subgrupo : 0,
                'quantidade'            => $quantidade,
                'posicao'               => $posicao ? $posicao->POSICA : null,
                'saldo_fisico'          => $saldoFisico ? $saldoFisico->SALFIS : 0,
                'valor_medio'           => $valorMedio ? $valorMedio : 0,
                'valor_total'           => $valorTotal ? $valorTotal : 0,
                'status'                => 'em análise'
            ]);
        }

        DB::table('itens_temporarios_lote')->where('user_id', $user->id)->delete();

        $itensDoLote = ImplantacaoSaldo::where('codigo_lote', $codigoLote)->get();

        Mail::send('emails.nova_implantacao_saldo', [
            'itens' => $itensDoLote, 
            'dadosGerais' => $itensDoLote->first()
        ], function ($message) use ($request, $user, $itensDoLote) {
            $destinatarios = [$request->gestor_filial, $request->gestor_regional, $request->diretor, $user->email];
            $filialNome = $itensDoLote->first()->descricao_localizacao ?? 'N/A';

            $message->to($destinatarios)
                    ->subject("📦 Novo Inventário - Filial: $filialNome (Lote: {$itensDoLote->first()->codigo_lote})");
        });

        return redirect()->route('implantacao_saldo.monitoramento')->with('success', 'Inventário enviado agrupado com sucesso!');
    }

    public function reenviarEmail($id)
    {
        $referencia = ImplantacaoSaldo::findOrFail($id);
        $itensDoLote = ImplantacaoSaldo::where('codigo_lote', $referencia->codigo_lote)->get();
        $dadosGerais = $itensDoLote->first();

        $emailDestino = null;
        $nivel = "";

        if (is_null($dadosGerais->aprovacao_filial)) {
            $emailDestino = $dadosGerais->gestor_filial;
            $nivel = "Local";
        } elseif (is_null($dadosGerais->aprovacao_regional)) {
            $emailDestino = $dadosGerais->gestor_regional;
            $nivel = "Regional";
        } elseif (is_null($dadosGerais->aprovacao_diretoria)) {
            $emailDestino = $dadosGerais->diretor;
            $nivel = "Diretoria";
        }

        if ($emailDestino) {
            Mail::send('emails.nova_implantacao_saldo', ['itens' => $itensDoLote, 'dadosGerais' => $dadosGerais], function ($message) use ($emailDestino, $nivel, $dadosGerais) {
                $message->to($emailDestino)
                        ->subject("🔔 LEMBRETE: Aprovação Pendente ($nivel) - Lote: {$dadosGerais->codigo_lote}");
            });

            return redirect()->back()->with('success', "E-mail de lembrete do lote enviado para $emailDestino ($nivel)");
        }

        return redirect()->back()->with('error', "Não há aprovações pendentes para este lote.");
    }

    /**
     * 🌟 SOLUÇÃO DE FLUXO: Método aprovar adaptado.
     * Se o gestor vier pelo link (GET) sem confirmar, mostramos a tela de decisão.
     * Se ele clicar no botão para confirmar, nós processamos a aprovação de todos os itens do lote de uma vez só!
     */
public function aprovar(Request $request, $id) {
        $user = auth()?->user();

        if(!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar.');
        }

        $referencia = ImplantacaoSaldo::findOrFail($id);
        $itensDoLote = ImplantacaoSaldo::where('codigo_lote', $referencia->codigo_lote)->get();
        $dadosGerais = $itensDoLote->first();
        $emailLogado = $user->email;

        // Se o usuário está apenas entrando na página através do link do e-mail, exibe a tela de revisão
        if (!$request->has('confirmar_acao')) {
            return view('implantacao_saldo.approve_screen', [
                'implantacao' => $dadosGerais,
                'itens' => $itensDoLote
            ]);
        }

        $fezAlgumaAprovacao = false;

        // Itera sobre todos os itens do lote para realizar a aprovação em lote
        foreach ($itensDoLote as $item) {
            if ($emailLogado === $item->gestor_filial && is_null($item->aprovacao_filial)) {
                $item->aprovacao_filial = 1;
                $fezAlgumaAprovacao = true;
            }

            if ($emailLogado === $item->gestor_regional && is_null($item->aprovacao_regional)) {
                $item->aprovacao_regional = 1;
                $fezAlgumaAprovacao = true;
            }

            if ($emailLogado === $item->diretor && is_null($item->aprovacao_diretoria)) {
                $item->aprovacao_diretoria = 1;
                $fezAlgumaAprovacao = true;
            }

            // Se o item recebeu todas as três assinaturas, finaliza o item
            if ($item->aprovacao_filial == 1 && 
                $item->aprovacao_regional == 1 && 
                $item->aprovacao_diretoria == 1) {
                
                $item->status = 'finalizado';
            }
            
            $item->save();
        }

        if (!$fezAlgumaAprovacao) {
            return view('implantacao_saldo.feedback', [
                'mensagem' => 'Você não possui pendências neste lote ou já realizou a sua aprovação.',
                'tipo' => 'error'
            ]);
        }

        // 🌟 CORREÇÃO 1: Consultar o banco de dados atualizado de forma limpa
        $loteCompletoFinalizado = ImplantacaoSaldo::where('codigo_lote', $dadosGerais->codigo_lote)
                                    ->where('status', '!=', 'finalizado')
                                    ->count() === 0;

        if ($loteCompletoFinalizado) {
            // 🌟 CORREÇÃO 2: Busca TODOS OS ITENS finalizados do lote para enviar no e-mail, não apenas o primeiro produto
            $loteCompletoAtualizado = ImplantacaoSaldo::where('codigo_lote', $dadosGerais->codigo_lote)->get();
            $this->notificarSetorNF($loteCompletoAtualizado);
        }

        return view('implantacao_saldo.feedback', ['mensagem' => 'Lote aprovado com sucesso!', 'tipo' => 'success']);
    }
    /**
     * 🌟 Reprovação em Lote adaptada
     */
    public function reprovar($id) {
        $referencia = ImplantacaoSaldo::findOrFail($id);
        $itensDoLote = ImplantacaoSaldo::where('codigo_lote', $referencia->codigo_lote)->get();
        $user = Auth::user();
        $emailLogado = $user->email;

        $permitido = false;
        foreach ($itensDoLote as $item) {
            if ($emailLogado === $item->gestor_filial || 
                $emailLogado === $item->gestor_regional || 
                $emailLogado === $item->diretor) {
                
                $permitido = true;
                if($emailLogado === $item->gestor_filial) $item->aprovacao_filial = 0;
                if($emailLogado === $item->gestor_regional) $item->aprovacao_regional = 0;
                if($emailLogado === $item->diretor) $item->aprovacao_diretoria = 0;

                $item->status = 'reprovado';
                $item->save();
            }
        }

        if ($permitido) {
            return view('implantacao_saldo.feedback', ['mensagem' => 'Lote de solicitações reprovado com sucesso.', 'tipo' => 'error']);
        }

        return view('implantacao_saldo.feedback', ['mensagem' => 'Sem permissão para reprovar este lote.', 'tipo' => 'error']);
    }

    private function notificarSetorNF($implantacao)
    {
        $destinatario = 'fiscal@grupocargopolo.com.br';

        // Detecta se recebeu uma coleção de múltiplos itens ou um objeto único
        $dadosGerais = $implantacao instanceof \Illuminate\Support\Collection ? $implantacao->first() : $implantacao;

        if (!$dadosGerais) {
            \Log::error("Tentativa de envio de e-mail de NF sem dados válidos.");
            return false;
        }

        $dadosGearsLoaded = $dadosGerais->loadMissing('user');

        // Consolida e limpa a lista de cópias (evita nulos, vazios e duplicados no CC)
        $ccList = array_filter(array_unique([
            $dadosGerais->gestor_filial,
            $dadosGerais->gestor_regional,
            $dadosGerais->diretor,
            $dadosGerais->user?->email
        ]));

        try {
            // Enviamos a coleção completa ($implantacao) para permitir um @foreach($dados as $item) na view do e-mail
            Mail::send('emails.implantacao_finalizada', ['dados' => $implantacao, 'dadosGerais' => $dadosGerais], function ($message) use ($dadosGerais, $destinatario, $ccList) {
                $message->to($destinatario);
                
                if (!empty($ccList)) {
                    $message->cc($ccList);
                }
                
                $message->subject('✅ LIBERADO: Implantação de Saldo Aprovada - Lote ' . $dadosGerais->codigo_lote);
            });
            return true;
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar e-mail de NF: " . $e->getMessage());
            return false;
        }
    }

    public function monitoramento() {
        $user = auth()?->user();

        if(!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar.');
        }

        if($user->temSetor(['admin','fiscal','diretoria','suprimentos'])) {
            $solicitacoes = ImplantacaoSaldo::where('status','<>','finalizado_fiscal')->orderBy('created_at', 'desc')->get();
        } else {
            $solicitacoes = ImplantacaoSaldo::where(function($query) use ($user) {
                $query->where('gestor_filial', $user->email)
                      ->orWhere('gestor_regional', $user->email)
                      ->orWhere('diretor', $user->email)
                      ->orWhere('user_id', $user->id);
            })->orderBy('created_at', 'desc')->get();
        }

        return view('implantacao_saldo.monitoramento', compact('solicitacoes'));
    }

    public function adicionarItemTemporario(Request $request)
    {
        $request->validate([
            'codprod' => 'required',
            'descricao' => 'required',
            'quantidade' => 'required|numeric|min:1',
            'movimentacao' => 'required|in:Entrada,Saída' // 🌟 Validação do novo campo
        ]);

        DB::table('itens_temporarios_lote')->updateOrInsert(
            [
                'user_id' => Auth::id(),
                'codprod' => $request->codprod
            ],
            [
                'descricao' => $request->descricao,
                'grupo' => $request->grupo ?? 0,
                'subgrupo' => $request->subgrupo ?? 0,
                'quantidade' => $request->quantidade,
                'movimentacao' => $request->movimentacao, // 🌟 Grava no banco
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Item saved into temporary batch successfully.']);
    }

    public function removerItemTemporario(Request $request)
    {
        DB::table('itens_temporarios_lote')
            ->where('user_id', Auth::id())
            ->where('codprod', $request->codprod)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function finalizar($id){

        $user = auth()?->user();

        if ($user->temSetor(['admin','suprimentos','fiscal'])){

            $implantacao = ImplantacaoSaldo::where('id', $id)->firstOrFail();

            $implantacao->update(['status' => 'finalizado_fiscal']);

            return redirect()->route('implantacao_saldo.monitoramento')->with('success','Solicitação aprovada com sucesso!');
        } else {
            abort(403, 'Acesso negado.');
        }
    }



public function importarCsvTemp(Request $request)
{
    $request->validate([
        'arquivo_csv' => 'required|file|mimes:csv,txt|max:5120',
    ]);

    try {
        $path = $request->file('arquivo_csv')->getRealPath();
        if (($handle = fopen($path, 'r')) === false) {
            return response()->json(['success' => false, 'message' => 'Não foi possível ler o arquivo.']);
        }

        $header = fgetcsv($handle, 1000, ';');
        if (!$header) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'O arquivo CSV está vazio.']);
        }

        $header = array_map(function($item) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', str_replace('"', '', $item)));
        }, $header);

        $itensAdicionados = [];

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            if (count($row) < count($header)) continue;

            $linha = array_combine($header, $row);
            
            $codprod = trim($linha['CODPROD'] ?? '');
            $mov     = trim($linha['MOVIMENTACAO'] ?? 'Entrada');
            $qtd     = intval($linha['QUANTIDADE'] ?? 0);

            if (empty($codprod) || $qtd <= 0) continue;

            // 🔍 Busca o produto no SQL Server
            $produto = DB::connection('sqlsrv')->table('ESTPRO as PRO')
                ->lock('WITH (NOLOCK)') 
                ->join('ESTGRP as GCP', 'PRO.CODGPP', '=', 'GCP.CODGPP')
                ->join('ESTSGP as SGP', 'PRO.CODSGP', '=', 'SGP.CODSGP')
                ->select([
                    'PRO.CODPROD as codprod', 
                    'PRO.DESCRI as descri',
                    'GCP.CODGPP as codgpp',
                    'SGP.CODSGP as codsgp'
                ])
                ->where('PRO.CODPROD', $codprod)
                ->first();

            if ($produto) {
                // 💾 SALVA COM O NOME CORRETO DA TABELA: itens_temporarios_lote
                DB::connection('mysql')->table('itens_temporarios_lote')->updateOrInsert(
                    ['user_id' => auth()->id(), 'codprod' => $produto->codprod],
                    [
                        'descricao'    => $produto->descri,
                        'grupo'        => $produto->codgpp,
                        'subgrupo'     => $produto->codsgp,
                        'quantidade'   => $qtd, // Atualiza ou insere a quantidade do CSV
                        'updated_at'   => now()
                    ]
                );

                $itensAdicionados[] = [
                    'codprod' => $produto->codprod,
                    'descri'  => $produto->descri,
                    'qtd'     => $qtd,
                    'mov'     => $mov // Retorna para o JavaScript aplicar na tabela da tela
                ];
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true, 
            'message' => count($itensAdicionados) . ' itens carregados com sucesso!',
            'itens'   => $itensAdicionados
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Erro crítico: ' . $e->getMessage()]);
    }
}



/**
 * Gera e descarrega um ficheiro CSV de exemplo para ajuste de saldo.
 */
public function exemploCsv()
{
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="exemplo_ajuste_saldo.csv"',
    ];

    // Cabeçalhos exatos esperados pelo validador
    $cabecalhos = ['CODPROD', 'MOVIMENTACAO', 'QUANTIDADE'];

    // Exemplos práticos (Entrada e Saída)
    $dadosExemplo = [
        ['1001', 'Entrada', '50'],
        ['1002', 'Saída', '12']
    ];

    $callback = function() use ($cabecalhos, $dadosExemplo) {
        $file = fopen('php://output', 'w');
        
        // Adiciona o BOM para o Microsoft Excel abrir os acentos corretamente
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Escreve os cabeçalhos separados por ponto e vírgula ';'
        fputcsv($file, $cabecalhos, ';');
        
        foreach ($dadosExemplo as $linha) {
            fputcsv($file, $linha, ';');
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
<?php

namespace App\Http\Controllers;

use App\Models\Rdv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Adiantamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\UnidadesNegocio;
use App\Models\Relatorio;
use Illuminate\Support\Facades\Mail;
use ZipArchive;
use Illuminate\Support\Facades\File;


class RdvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('rdv.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produtos = Rdv::produtos();


        $adiantamentoModel = Adiantamento::where('status', 'aprovado')->where('user_id',auth()->id())->orderBy('id','desc')
            ->first();

        if($adiantamentoModel){
            return view('rdv.despesas', compact('produtos','adiantamentoModel'));

        }


        return view('rdv.despesas', compact('produtos'));
    }

    public function relatorio()
    {
        $filiais = UnidadesNegocio::orderBy('unidade_negocio')->get();

        $despesas = Rdv::where('user_id', auth()->id())
                        ->where('status', 'pendente')
                        ->get();

        // $adiantamentoModel = Adiantamento::where('fornecedor', $fornecedorId)
        //     ->where('status', 'aprovado')->orderBy('id','desc')
        //     ->first();

        return view('rdv.relatorio', compact('filiais','despesas'));
    }

    public function resumo(Request $request)
    {
        //
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        if ($user->temSetor(['admin','financeiro'])){

            $relatorios = Relatorio::where('status','aprovado')->orderBy('created_at', 'desc')->paginate(3);
            $despesas = Rdv::where('status','pendente')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(3);
            $adiantamentos = Adiantamento::where('status','utilizado')->orderBy('created_at', 'desc')->paginate(3);
        } else {
            $relatorios = Relatorio::where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(3);
            $despesas = Rdv::where('status','pendente')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(3);
            $adiantamentos = Adiantamento::where('status','utilizado')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(3);

        }

        $relatorios->load('unidades', 'despesas', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');


        return view('rdv.resumo', compact('relatorios','despesas','adiantamentos'));
    }


    public function cancelarRelatorio($id)
    {
        $relatorio = Relatorio::findOrFail($id);

        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        if ($user->temSetor(['financeiro','admin'])){
            if ($relatorio->user_id !== auth()->id()) {
                return redirect()->route('rdv.resumo')->with('error', 'Você não tem permissão para cancelar este relatório.');
            }
        }

        //$reserva->delete();
        // Altera o status da coluna "Ok" para "Cancelada"
        $relatorio->status = 'cancelada';
        $relatorio->save();

                // Obtém o usuário autenticado
    $user = Auth::user();

    $relatorio->load('unidades', 'despesas', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

    // Envia o e-mail de cancelamento
    Mail::send('emails.cancelamento_relatorio', ['relatorio' => $relatorio, 'user' => $user], function($message) use ($user, $relatorio) {
        $message->to([$relatorio->user_email,$relatorio->gestor_aprovador , $user->email]);
        $message->subject('Relatório de Despesas Cancelado');
    });


    return redirect()->route('rdv.index')->with('success', 'Relatório cancelado com sucesso.');
    }

    public function finalizarRelatorio($id)
    {
        $relatorio = Relatorio::findOrFail($id);

        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        if ($user->temSetor(['financeiro','admin'])){

            if ($relatorio->user_id !== auth()->id()) {
                return redirect()->route('rdv.index')->with('error', 'Você não tem permissão para finalizar este relatório.');
            }
        }

        //$relatorio->delete();
        // Altera o status da coluna "Ok" para "Cancelada"
        $relatorio->status = 'finalizado';
        $relatorio->save();

        // Obtém o usuário autenticado
        $user = Auth::user();

        // Envia o e-mail de finalização
        Mail::send('emails.finalizar_relatorio', ['relatorio' => $relatorio, 'user' => $user], function($message) use ($user, $relatorio) {
            $message->to([$relatorio->user_email,$relatorio->gestor_aprovador, $user->email]);
            $message->subject('Relatório Finalizado');
        });

        return redirect()->route('rdv.index')->with('success', 'Relatório finalizado com sucesso.');

    }

        // No Controller de Despesas
    public function cancelarDespesa($id)
    {
        $despesa = Rdv::findOrFail($id);
        $despesa->update([
            'status' => 'cancelada', 
        ]);

        return redirect()->route('rdv.index')->with('success', 'Despesa cancelada com sucesso!');
    }



    public function store_relatorio(Request $request)
    {
        // 1. Validação dos dados
        $validated = $request->validate([
            'titulo'                => 'required|string|max:255',
            'motivo'                => 'required|string',
            'cod_unidade'           => 'required',
            'cod_custo'             => 'required',
            'cod_gasto'             => 'required',
            'gestor_aprovador'      => 'required',
            'inicio_viagem'         => 'required|date',
            'fim_viagem'            => 'required|date|after_or_equal:inicio_viagem',
            'despesas_selecionadas' => 'required|array|min:1', // IDs das despesas vindos do checkbox
        ]);

        try {
            // Inicia a transação: ou grava tudo, ou não grava nada!
            DB::beginTransaction();

            // 1. CALCULAR O TOTAL (Novidade aqui)
            // Buscamos a soma de todos os 'valor' das despesas selecionadas
            $valorTotal = Rdv::whereIn('id', $request->despesas_selecionadas)
                            ->where('user_id', Auth::id())
                            ->sum('valor');

            // 2. Criar o Relatório (O "Pai")
            $relatorio = Relatorio::create([
                'titulo'           => $validated['titulo'],
                'motivo'           => $validated['motivo'],
                'cod_unidade'      => $validated['cod_unidade'],
                'cod_custo'        => $validated['cod_custo'],
                'cod_gasto'        => $validated['cod_gasto'],
                'gestor_aprovador' => $validated['gestor_aprovador'],
                'inicio_viagem'    => $validated['inicio_viagem'],
                'fim_viagem'       => $validated['fim_viagem'],
                'valor'            => $valorTotal,
                'user_id'          => Auth::id(),
                'user_name'        => Auth::user()->name,
                'user_email'       => Auth::user()->email,
                'status'           => 'pendente',
                'approval_token'   => Str::uuid(),
            ]);


            // 3. Atualizar as Despesas (Os "Filhos")
            // Aqui pegamos todos os IDs que vieram no array despesas_selecionadas
            Rdv::whereIn('id', $request->despesas_selecionadas)
                ->where('user_id', Auth::id()) // Segurança: garante que o usuário só altere as próprias despesas
                ->update([
                    'relatorio_id' => $relatorio->id,
                    'status'       => 'em_relatorio' 
                ]);

            


            $relatorio->load('unidades', 'despesas', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $relatorio->update([
                'pix_reembolso' => ($relatorio->despesas->first()->pix ?? '0')
            ]);
            

            // Se chegou aqui sem erro, confirma no banco
            DB::commit();

            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.relatorio_pendente', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
                $message->to($relatorio->gestor_aprovador);
                $message->subject('Solicitação de Relatório de Despesa Pendente - Protocolo: ' . $relatorio->id);
                

            });

            Mail::send('emails.relatorio_solicitacao', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
                $message->to($relatorio->user_email);
                $message->subject('Confirmação de Solicitação Financeiro - Protocolo: ' . $relatorio->id);


            });





            return redirect()->route('rdv.index')->with('success', 'Relatório criado e despesas vinculadas!');

        } catch (\Exception $e) {
            // Se der qualquer erro (banco caiu, campo faltando), desfaz tudo
            DB::rollBack();
            
            return redirect()->back()
                            ->withErrors(['error' => 'Falha ao salvar relatório: ' . $e->getMessage()])
                            ->withInput();
        }
    }




    public function buscarFornecedores(Request $request)
    {
        $search = $request->search;

        $fornecedores = Adiantamento::fornecedoresQuery($search);

        return response()->json($fornecedores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validação agora espera arrays
        $request->validate([
            'fornecedor' => 'required',
            'pix'        => 'required',
            'despesa.*'  => 'required',
            'valor.*'    => 'required',
            'date.*'     => 'required',
            'foto.*'     => 'required',
        ]);

        $fornecedorId = $request->fornecedor;
        
        // Busca Razão Social do Fornecedor uma única vez (otimização)
        $descricaoFornecedor = DB::connection('sqlsrv')->table('RODCLI')
                                ->where('CODCLIFOR', $fornecedorId)->value('RAZSOC');

        // Loop através do array de despesas
        foreach ($request->despesa as $key => $valorDespesa) {
            
            // Busca detalhes de cada tipo de despesa no loop
            $produto = DB::connection('sqlsrv')->table('ESTPRO')
                        ->where('CODPROD', $valorDespesa)->first();
                        
            $classificacao = DB::connection('sqlsrv')->table('ESTCPP')
                            ->where('CODPROD', $valorDespesa)->first();

            // Cria a despesa
            $despesa = Rdv::create([
                'fornecedor'           => $fornecedorId,
                'despesa'              => $valorDespesa,
                'date'                 => $request->date[$key],
                'valor'                => $request->valor[$key],
                'user_name'            => $user->name,
                'user_id'              => $user->id,
                'user_email'           => $user->email,
                'descricao_fornecedor' => $descricaoFornecedor,
                'descricao_despesa'    => $produto->DESCRI ?? '',
                'SINTET'               => $classificacao->SINTET ?? null,
                'ANALIT'               => $classificacao->ANALIT ?? null,
                'approval_token'       => Str::uuid(),
                'pix'                  => $request->pix, // Salvando o valor do PIX na despesa
            ]);

            // Upload da Foto específica dessa linha ($key)
            if ($request->hasFile("foto.$key")) {
                $file = $request->file("foto.$key");
                $filename = time() . '_' . Str::uuid() . '.' . $file->guessExtension();
                $file->move(storage_path('app/public/despesas'), $filename);
                $despesa->update(['anexo' => 'despesas/' . $filename]);
            }
        }

        return redirect()->route('rdv.index')->with('success', 'Todas as despesas foram cadastradas!');
    }

    public function telaAprovacao($token)
    {
        // Busca o relatório pelo token com as despesas
        $relatorio = Relatorio::where('approval_token', $token)->firstOrFail();
        
        // Carrega as despesas vinculadas
        $despesas = $relatorio->despesas; 

        // Soma apenas as despesas que o gestor já clicou em "Aprovar"
        $valorAprovado = $despesas->where('status', 'aprovado')->sum('valor');
        
        $existePendente = $despesas->contains('status', 'pendente');

        return view('rdv.aprovacao_gestor', compact('relatorio', 'despesas', 'valorAprovado', 'existePendente'));
    }

    public function updateStatus(Request $request, $id)
    {
        $despesa = Rdv::findOrFail($id);
        $despesa->update([
            'status' => $request->status, 
            'observacao'=> $request->observacao
        ]);

        // Retorna JSON em vez de Redirect
        return response()->json([
            'success' => true,
            'message' => 'Status da despesa atualizado!',
            'novo_status' => $despesa->status
        ]);
    }

public function finalizar(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $statusFinal = $request->input('status_final'); // 'aprovado' ou 'reprovado'

        // 1. Carregamos as relações
        $relatorio = Relatorio::with(['despesas', 'unidades', 'unidadeAprovadora', 'centroGasto', 'centroCusto'])
            ->where('status', 'pendente')
            ->findOrFail($id);

        // 2. Validação de pendências de análise técnica
        if ($relatorio->despesas->where('status', 'pendente')->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Existem despesas que ainda não foram analisadas!']);
        }

        // --- BLOCO DE REPROVAÇÃO TOTAL DO RELATÓRIO ---
        if ($statusFinal === 'reprovado') {
            // 1. Resetar as despesas que foram APROVADAS (voltam a ser pendentes e sem relatório)
            $relatorio->despesas()->where('status', 'aprovado')->update([
                'status' => 'pendente',
                'relatorio_id' => null
            ]);

            // 2. Desvincular as despesas que foram REPROVADAS (mantêm o status reprovado para o usuário ver o erro)
            $relatorio->despesas()->where('status', 'reprovado')->update([
                'relatorio_id' => null
            ]);

            // 2. Desvincular as despesas que foram IGNORADAS (mantêm o status ignorado para o usuário ver o erro)
            $relatorio->despesas()->where('status', 'em_relatorio')->update([
                'status' => 'pendente',
                'relatorio_id' => null
            ]);

            // 3. Atualizar o status do relatório pai
            $relatorio->update([
                'status' => 'reprovado',
                'observacao_gestor' => 'Relatório reprovado. As despesas aprovadas foram liberadas e as reprovadas devem ser corrigidas.'
            ]);

            DB::commit();
            
            return redirect()->route('rdv.index')
                ->with('error', 'Relatório reprovado. As despesas foram desvinculadas para correção.');
        }

        // --- BLOCO DE APROVAÇÃO (SÓ EXECUTA SE TUDO FOR 'aprovado') ---
        
        // Validação extra de segurança: Se o usuário tentar aprovar o relatório, 
        // mas houver QUALQUER despesa reprovada no meio.
        if ($relatorio->despesas->where('status', 'reprovado')->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Não é permitido aprovar um relatório que contenha despesas reprovadas. Reprove o relatório para ajuste.']);
        }

        // 3. Recálculo do valor (apenas aprovados)
        $despesasAprovadas = $relatorio->despesas->where('status', 'aprovado');
        $novoValorTotal = $despesasAprovadas->sum('valor');



        // [ INÍCIO DAS INTEGRAÇÕES FINANCEIRAS - MANTIDAS IGUAIS ]
        // Pegamos o fornecedor da primeira despesa aprovada
        $primeiraDespesa = $despesasAprovadas->first();
        $fornecedorId = $primeiraDespesa->fornecedor ?? '0';

        $adiantamentoModel = Adiantamento::where('fornecedor', $fornecedorId)
            ->where('status', 'aprovado')->orderBy('id','desc')
            ->first();
        
        if ($adiantamentoModel) {
            if($adiantamentoModel->valor > $relatorio->valor) {
                
                $valorPix = $adiantamentoModel->valor - $relatorio->valor;

                try {
                    // Enviando para o user_email do relatório
                    Mail::send('emails.relatorio_adiantamento_pix', [
                        'relatorio' => $relatorio, 
                        'valorPix' => $valorPix,
                        'adiantamentoTotal' => $adiantamentoModel->valor
                    ], function ($message) use ($relatorio) {
                        $message->to($relatorio->user_email); // Destinatário principal: Solicitante
                        $message->cc([$relatorio->gestor_aprovador]);
                        $message->subject('AÇÃO NECESSÁRIA: Devolução de Saldo PIX - Protocolo: ' . $relatorio->id);
                    });


                } catch (\Exception $e) {
                    \Log::error("Erro ao enviar email de PIX: " . $e->getMessage());
                }


            return "<script>
                        alert('O valor do adiantamento é maior que o valor do relatório. Instruções de PIX enviadas para: " . $relatorio->user_email . "');
                        window.history.back();
                    </script>";       

             }
        
        }

        // 4. Atualiza o Relatório para Aprovado
        $relatorio->update([
            'valor'  => $novoValorTotal,
            'status' => 'aprovado'
        ]);
        
        $relatorio->entradaMateriais(
            $relatorio->id, 
            $relatorio->valor, 
            $fornecedorId, 
            $relatorio->unidadeAprovadora->nome_gestor ?? 'Gestor não definido'
        );

        $x = 0;
        foreach ($despesasAprovadas as $item) {
            $ultimoId = DB::connection('sqlsrv')->table('ESTAIE')->max('ID_AIE');
            $proximo = $ultimoId + 1;
            $x++;

            $item->produtosMateriais(
                $item->id, $item->fornecedor, $item->despesa, $item->valor,
                $proximo, $item->relatorio_id, $x
            );
        }

        // Limpeza: Desvincular despesas reprovadas (embora o bloqueio acima impeça isso de chegar aqui, é uma boa segurança)
        Rdv::where('relatorio_id', $relatorio->id)
            ->where('status', 'reprovado')
            ->update(['relatorio_id' => null]);

        $relatorio->update([
            'id_rodopar' => ($fornecedorId) . '-A-' . $relatorio->id
        ]);

        $adiantamentoModel = Adiantamento::where('fornecedor', $fornecedorId)
            ->where('status', 'aprovado')->orderBy('id','desc')
            ->first();

        $situacaoPadrao = 'D';

        if ($adiantamentoModel) {
            $relatorio->adiantamentos($fornecedorId, $relatorio->id, $relatorio->valor, $adiantamentoModel->id_raz);
            $adiantamentoModel->update(['status' => 'utilizado']);
            $adiantamentoModel->refresh();

            $situacaoParaEnviar = $adiantamentoModel->situac ?? $situacaoPadrao;
            $valorLiquidoParaEnviar = $adiantamentoModel->valor_liquido ?? $relatorio->valor;
            $valorUtilizadoParaEnviar = $adiantamentoModel->valor_utilizado ?? 0;
        } else {
            $situacaoParaEnviar = $situacaoPadrao;
            $valorLiquidoParaEnviar = $relatorio->valor;
            $valorUtilizadoParaEnviar = 0;
        }

        // Finalizações no Rodopar
        $relatorio->finalizar($fornecedorId, $relatorio->valor, $relatorio->id, $relatorio->cod_unidade, $relatorio->cod_custo, $relatorio->cod_gasto, $relatorio->unidadeAprovadora->nome_gestor ?? 'Gestor não definido', $valorLiquidoParaEnviar, $valorUtilizadoParaEnviar, $situacaoParaEnviar);

        foreach ($despesasAprovadas as $item) {
            $item->pagrat($fornecedorId, $item->valor, $relatorio->id, $relatorio->cod_unidade, $relatorio->cod_custo, $relatorio->cod_gasto, $item->despesa, $item->SINTET, $item->ANALIT);
        
            if($item->despesa == '946762') { // Código específico para reembolso de hospedagem
                $item->pagratReembolso($relatorio->id, $item->valor, $relatorio->unidadeAprovadora->nome_gestor ?? 'Gestor não definido', $relatorio->cod_unidade, $relatorio->cod_custo, $relatorio->cod_gasto, $item->id, $item->descricao_fornecedor);
            }
        }

        $relatorio->finalizar_2($fornecedorId, $relatorio->valor, $relatorio->id, $relatorio->cod_unidade, $relatorio->cod_custo, $relatorio->cod_gasto, $relatorio->unidadeAprovadora->nome_gestor ?? 'Gestor não definido', $valorLiquidoParaEnviar, $valorUtilizadoParaEnviar, $situacaoParaEnviar);
        // [ FIM DAS INTEGRAÇÕES ]

        DB::commit(); 




        // --- LÓGICA DO ZIP FINAL ---
        $zip = new \ZipArchive;
        $zipName = 'relatorio_' . $relatorio->id . '.zip';
        $zipDirectory = storage_path('app/public/zips');

        if (!File::exists($zipDirectory)) {
            File::makeDirectory($zipDirectory, 0777, true, true);
        }

        $zipPath = $zipDirectory . DIRECTORY_SEPARATOR . $zipName;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $arquivosAdicionados = 0;

            foreach ($despesasAprovadas as $item) {
                // Como o $item->anexo já traz "despesas/nome.avif"
                // usamos storage_path('app/public/') para não duplicar a pasta
                $caminhoAnexoOriginal = storage_path('app/public/' . $item->anexo);

                if (!empty($item->anexo) && file_exists($caminhoAnexoOriginal)) {
                    // basename() extrai apenas o nome do arquivo, removendo o "despesas/"
                    // assim o ZIP fica limpo, sem pastas dentro dele
                    $zip->addFile($caminhoAnexoOriginal, basename($item->anexo));
                    $arquivosAdicionados++;
                }
            }
            
            $zip->close();

            if ($arquivosAdicionados === 0) {
                if(file_exists($zipPath)) { unlink($zipPath); }
                throw new \Exception("Arquivos não encontrados no servidor. Verifique o caminho: " . storage_path('app/public/'));
            }
        }

        $zipUrl = asset('storage/zips/' . $zipName);






        // 9. Envio de E-mail de Sucesso
        try {
            Mail::send('emails.relatorio_finalizar', [
                'relatorio' => $relatorio, 
                'zipUrl'    => $zipUrl,
                'zipName'   => $zipName
            ], function ($message) use ($relatorio) {
                $message->to('contasapagar@grupocargopolo.com.br');
                //$message->to('higor.machado@grupocargopolo.com.br');
                $message->cc([$relatorio->gestor_aprovador ?? null,$relatorio->user_email]);
                $message->subject('Relatório de Despesa Aprovado - Protocolo: ' . $relatorio->id);
            });
        } catch (\Exception $e) { }

        return redirect()->route('rdv.index')
            ->with('success', 'Relatório finalizado com sucesso! Valor: R$ ' . number_format($novoValorTotal, 2, ',', '.'));

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['error' => 'Falha ao processar relatório: ' . $e->getMessage()])
            ->withInput();
    }
}



    // No DespesaController.php

    public function createPix(Request $request, $relatorio_id) {
        $relatorio = Relatorio::findOrFail($relatorio_id);
        $valorPix = $request->query('valor');

        // Retorna uma view simples só com o campo de FOTO
        return view('rdv.create_pix', compact('relatorio', 'valorPix'));
    }

    public function storePix(Request $request) 
    {
        $user = Auth::user();

        // Validamos APENAS a foto e o ID do relatório (que vêm escondidos no form)
        $request->validate([
            'relatorio_id' => 'required',
            'valor' => 'required',
            'foto' => 'required', // Máx 5MB
        ]);

        $relatorio = Relatorio::findOrFail($request->relatorio_id);
        $fornecedor_despesa = Rdv::where('relatorio_id', $relatorio->id)->first();

        // DADOS AUTOMÁTICOS DEFINIDOS POR VOCÊ
        $fornecedorId = $fornecedor_despesa->fornecedor; // Pega do relatório
        $codigoDespesaFixa = '946762';         // Código fixo que você pediu
        $dataAtual = now();                    // Data de agora

        // BUSCA OS DADOS NO SQL SERVER (Igual ao seu store original)
        $descricaoFornecedor = DB::connection('sqlsrv')->table('RODCLI')
            ->where('CODCLIFOR', $fornecedorId)->value('RAZSOC');

        $descricaoDespesa = DB::connection('sqlsrv')->table('ESTPRO')
            ->where('CODPROD', $codigoDespesaFixa)->value('DESCRI');

        $classificacao = DB::connection('sqlsrv')->table('ESTCPP')
            ->where('CODPROD', $codigoDespesaFixa)->first();

        // CRIA A DESPESA (RDV)
        $despesa = Rdv::create([
            'relatorio_id' => $relatorio->id, // Inserindo o ID do relatório
            'fornecedor'   => $fornecedorId,
            'despesa'      => $codigoDespesaFixa,
            'date'         => $dataAtual,
            'valor'        => $request->valor,
            'user_name'    => $user->name,
            'user_id'      => $user->id,
            'user_email'   => $user->email,
            'descricao_fornecedor' => $descricaoFornecedor,
            'descricao_despesa'    => $descricaoDespesa,
            'SINTET'       => $classificacao->SINTET ?? null,
            'ANALIT'       => $classificacao->ANALIT ?? null,
            'approval_token' => (string) Str::uuid(),
        ]);

        // TRATAMENTO DO UPLOAD (Igual ao seu original)
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $file = $request->file('foto');
            $extension = $file->guessExtension() ?? 'bin';
            $filename = time() . '_' . (string) Str::uuid() . '.' . $extension;

            $file->move(storage_path('app/public/despesas'), $filename);
            $path = 'despesas/' . $filename;
            
            $despesa->update(['anexo' => $path]);
        }

        $relatorio->update([
            'valor' => $relatorio->valor + $request->valor
        ]);

        

        // Email só para o gestor aprovar/reprovar
        Mail::send('emails.relatorio_pendente', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
            $message->to($relatorio->gestor_aprovador);
            $message->subject('Solicitação de Relatório de Despesa Pendente - Protocolo: ' . $relatorio->id);
            

        });


        return redirect()->route('rdv.index')->with('success', 'Comprovante de PIX anexado como despesa!');
    }



    /**
     * Display the specified resource.
     */
    public function show(Nofly $nofly)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nofly $nofly)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nofly $nofly)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nofly $nofly)
    {
        //
    }






    public function estent(){


    // INSERT INTO ESTENT (DATINC,USUINC,CODPAD,USUATU,DATATU,CODMOT,CODFEC,NUMDFE,CODLIN,REFSER,REFDOC,CODLOT,RESPFR,SUBCON,CODTPS,CODMUN,CLIREB,NFE_ID,CODOPE,TIPONF,CODTAR,CODFPG,SITUAC,CODBCO,CODTAX,CODCLIFOR,REFERE,NUMDOC,CODFIL,OBSERV,SERIE,VLRDOC,DATEMI,DATREF,VLRLIQ,DESCAN,JURDIA,VLRPED,DESPEX,ICMSEX,PERJUR,DESISS,DESPIS,DESIR,DESINS,DESCSL,DESCOF,ICMSST,DESADT,DESIPI,REFDAT,DEDBAS,DESPIN,DESCOB,VLRFRE,VLRSEG,NUMCNO) 

    // VALUES('02/20/2026 09:55:00','HIGOR.MACHADO',1,'HIGOR.MACHADO','02/20/2026 09:55:00',null,null,NULL,NULL,NULL,0,0,'S','N',null,null,null,NULL,null,'REL',1717,null,'I',237,1,154189,NULL,'123',5,NULL,'A',0,'02/20/2026','02/20/2026',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,0,NULL)




    // INSERINDO ADIANTAMENTO

    // INSERT INTO BANRNF (CODCLIFOR,SERIE,NUMDOC,TIPONF,SITNOT,ID_RAZ,DATATU,USUATU,VLRDOC,TEMPOR,NOMEPC,ORIGEM,NUMDUP,FILDUP,NUMPAR) VALUES (154189,'A','123','REL','I',507299,getdate(),'HIGOR.MACHADO',420, Null ,'DESKTOP-AEN566N','NFE', Null , Null , Null )

    // UPDATE ESTENT SET CODPAD=1,USUATU='HIGOR.MACHADO',DATATU='02/20/2026 09:56:00',CODMOT=NULL,CODFEC=NULL,NUMDFE=NULL,CODLIN=NULL,REFSER=NULL,REFDOC=0,CODLOT=0,RESPFR='S',SUBCON='N',CODTPS=NULL,CODMUN=NULL,CLIREB=NULL,NFE_ID=NULL,CODOPE=NULL,CODTAR=1717,CODFPG=NULL,SITUAC='I',CODBCO=237,CODTAX=1,REFERE=NULL,CODFIL=5,OBSERV=NULL,VLRDOC=0,DATEMI='02/20/2026',DATREF='02/20/2026',VLRLIQ=-420.00,DESCAN=0,JURDIA=0,VLRPED=0,DESPEX=0,ICMSEX=0,PERJUR=0,DESISS=0.00,DESPIS=0.00,DESIR=0,DESINS=0.00,DESCSL=0.00,DESCOF=0.00,ICMSST=0,DESADT=420,DESIPI=0,REFDAT=NULL,DEDBAS=0,DESPIN=0,DESCOB=0,VLRFRE=0,VLRSEG=0,NUMCNO=NULL  WHERE TIPONF='REL' AND CODCLIFOR=154189 AND NUMDOC='123' AND SERIE='A'

    // UPDATE OSEONF SET VLRDOC = 0 , DATNOT = '02/20/2026 00:00' WHERE CODCLIFOR = 154189 AND SERIE = 'A' AND NUMDOC = '123'


    // INSERINDO AS DESPESAS

    // INSERT INTO ESTAIE (ID_AIE,CODCLIFOR,TIPONF,SERIE,NUMDOC,CODPROD,QTDENT,VLRUNI,VLRTOT,VLRIPI,PRICMS,PRDCMR,DATATU,USUATU,NUMPED,VLRPIS,VLRCOF,PERIPI,DESCRI,CODFIS,BASSUB,REFINT,REFFOR,VLRFRE,DESPIN,VLRSEG,BASCAL,VLRICM,CSTICM,OUTROS,VLRISE,DIFALI,VLRDIF,ALISUB,ICMSUB,CSTSUB,BASIPI,CSTIPI,BASPIS,CSTPIS,BASCOF,CSTCOF,CODGRUFIS,DESTAC,CODCHA,CODDEP,CODAUT,CMBAMB,UFCONS,BASCID,PERCID,VLRCID,OBSFIS,CODVEI,VLRIMP,TOTPAR,ALICOT,VCTPAR,DEPCAL,ANALIT,ORDEM,ICMDED,UNIDAD,BASCBS, ALICBS, VLRCBS, CSTCBS, CLACBS,BASIBS, ALIIBS, VLRIBS, CSTIBS, CLAIBS, BASIS, ALIIS, VLRIS, CSTIS, CLAIS,ALIREM) VALUES(717017,'154189','REL','A','123','946367', Null ,420,0.000000,0,0, Null ,getdate(),'HIGOR.MACHADO',null,0.00,0.00,0,'ALMOÇO',1102, Null , Null , Null , Null , Null , Null ,0,0,'90',0,0, Null , Null , Null , Null ,'90', Null ,'03',0,'70',0,'70',30,'N' , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,Null, Null , Null ,'1', Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,0)

    // OUTRO EXEMPLO COM CAFE DA MANHA
    // INSERT INTO ESTAIE (ID_AIE,CODCLIFOR,TIPONF,SERIE,NUMDOC,CODPROD,QTDENT,VLRUNI,VLRTOT,VLRIPI,PRICMS,PRDCMR,DATATU,USUATU,NUMPED,VLRPIS,VLRCOF,PERIPI,DESCRI,CODFIS,BASSUB,REFINT,REFFOR,VLRFRE,DESPIN,VLRSEG,BASCAL,VLRICM,CSTICM,OUTROS,VLRISE,DIFALI,VLRDIF,ALISUB,ICMSUB,CSTSUB,BASIPI,CSTIPI,BASPIS,CSTPIS,BASCOF,CSTCOF,CODGRUFIS,DESTAC,CODCHA,CODDEP,CODAUT,CMBAMB,UFCONS,BASCID,PERCID,VLRCID,OBSFIS,CODVEI,VLRIMP,TOTPAR,ALICOT,VCTPAR,DEPCAL,ANALIT,ORDEM,ICMDED,UNIDAD,BASCBS, ALICBS, VLRCBS, CSTCBS, CLACBS,BASIBS, ALIIBS, VLRIBS, CSTIBS, CLAIBS, BASIS, ALIIS, VLRIS, CSTIS, CLAIS,ALIREM) VALUES(717018,'154189','REL','A','123','946366',1,15,15.000000,0,0, Null ,getdate(),'HIGOR.MACHADO',null,0.00,0.00,0,'CAFÉ DA MANHA',1102, Null , Null , Null , Null , Null , Null ,0,0,'90',0,15, Null , Null , Null , Null ,'90', Null ,'03',0,'70',0,'70',30,'N' , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,Null, Null , Null ,'2', Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,0)

    // INSERT INTO ESTAIL VALUES(717017,'154189','REL','A','123',946367,1,0,getdate(),getdate(),'HIGOR.MACHADO')

    // INSERT INTO ESTAIL VALUES(717018,'154189','REL','A','123',946366,1,1,getdate(),getdate(),'HIGOR.MACHADO')

    // UPDATE ESTENT SET VLRDOC = 0, VLRLIQ = -420.00, DESIPI = 0  WHERE CODCLIFOR = 154189 AND NUMDOC = '123' AND SERIE = 'A' AND TIPONF = 'REL'

    // UPDATE ESTENT SET VLRDOC = 15, VLRLIQ = -405.00, DESIPI = 0  WHERE CODCLIFOR = 154189 AND NUMDOC = '123' AND SERIE = 'A' AND TIPONF = 'REL'


    // UPDATE OSEONF SET VLRDOC = 629 , DATNOT = '02/20/2026 00:00' WHERE CODCLIFOR = 154189 AND SERIE = 'A' AND NUMDOC = '123'


    // UPDATE ESTENT SET SITUAC = 'O' WHERE  NUMDOC = '123' AND CODCLIFOR = 154189 AND SERIE = 'A' AND TIPONF = 'REL'



    // INSERT INTO RATPAG (CODCLIFOR,TIPONF,SERIE,NUMDOC,CODVEI,CODEVE,CODFRO,CODLIN,CODFIL,NUMACE, DATREF,TIPEVE,TIPDOC,VLRTOT,ATUKMT,CODMOT,ID_ACE,USUINC,DATINC,USUATU,DATATU,QUANTI,VLRUNI) SELECT  CODCLIFOR, 'ACV', 'A', '123' ,CODVEI, CODEVE,CODFRO,CODLIN,CODFIL,NUMACE,DATREF,TIPEVE,TIPDOC,QUANTI*VLRUNI,ATUKMT,CODMOT,ID_ACE,USUINC,DATINC,USUATU,DATATU,QUANTI,VLRUNI FROM ESTNEV WHERE NUMDOC = '123' AND SERIE = 'A' AND CODCLIFOR = 154189 AND TIPONF = 'REL'
    

    }





}









// CONTINUIDADE



// ADIANTAMENTO
// INSERT INTO BANRNF (CODCLIFOR,SERIE,NUMDOC,TIPONF,SITNOT,ID_RAZ,DATATU,USUATU,VLRDOC,TEMPOR,NOMEPC,ORIGEM,NUMDUP,FILDUP,NUMPAR) VALUES (154189,'A','31','REL','I',507299,getdate(),'HIGOR.MACHADO',90, Null ,'DESKTOP-AEN566N','NFE', Null , Null , Null )

// UPDATE ESTENT SET CODPAD=1,USUATU='HIGOR.MACHADO',DATATU='02/25/2026 17:43:00',CODMOT=NULL,CODFEC=NULL,NUMDFE=NULL,CODLIN=NULL,REFSER=NULL,REFDOC=0,CODLOT=0,RESPFR='S',SUBCON='N',CODTPS=NULL,CODMUN=NULL,CLIREB=NULL,NFE_ID=NULL,CODOPE=NULL,CODTAR=1717,CODFPG=NULL,SITUAC='I',CODBCO=237,CODTAX=1,REFERE=NULL,CODFIL=5,OBSERV='Reembolso de Despesa - Aprovado por Higor Machado',VLRDOC=90,DATEMI='02/25/2026',DATREF='02/25/2026',VLRLIQ=0.00,DESCAN=0,JURDIA=0,VLRPED=0,DESPEX=0,ICMSEX=0,PERJUR=0,DESISS=0.00,DESPIS=0.00,DESIR=0,DESINS=0.00,DESCSL=0.00,DESCOF=0.00,ICMSST=0,DESADT=90,DESIPI=0,REFDAT=NULL,DEDBAS=0,DESPIN=0,DESCOB=0,VLRFRE=0,VLRSEG=0,NUMCNO=NULL  WHERE TIPONF='REL' AND CODCLIFOR=154189 AND NUMDOC='31' AND SERIE='A'
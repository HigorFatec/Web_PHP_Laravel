<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadesNegocio;
use Illuminate\Support\Facades\Auth;
use App\Models\Reembolso;
use ZipArchive;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 
use App\Models\Financeiro;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class ReembolsoController extends Controller
{
    public function reembolso(Request $request)
    {
        $filiais = UnidadesNegocio::orderBy('unidade_negocio')->get(); //->pluck('filial');
        // NÃO carregue fornecedores aqui – AJAX fará isso
        $fornecedores = []; // opcional, apenas para evitar erro no Blade

        $despesas = Reembolso::where('user_id', auth()->id())
                        ->where('status', 'pendente')
                        ->get();

        //dd($fornecedores->count(), $fornecedores->first());

        return view('financeiro_fr.reembolso', compact('filiais','fornecedores','despesas'));
        //
    }



    public function despesas()
    {
        $produtos = Reembolso::produtos();



        return view('financeiro_fr.despesas', compact('produtos'));
    }

    public function despesas_store(Request $request)
    {
        $user = Auth::user();

        // Validação agora espera arrays
        $request->validate([
            'fornecedor' => 'required',
            'pix'        => 'required',
            'despesa.*'  => 'required',
            'valor.*'    => 'required|numeric',
            'date.*'     => 'required',
            'foto.*'     => 'required',
        ],[
            'valor.numeric' => 'O campo valor deve ser um número válido (ex: 1250.50).',
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
            $despesa = Reembolso::create([
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

        return redirect()->route('reembolso.create')->with('success', 'Todas as despesas foram cadastradas!');
    }




    public function store_reembolso(Request $request)
    {
        // 1. Validação dos dados
        $validated = $request->validate([
            'favorecido'            => 'required|string|max:255',
            'referencia'            => 'required|string',
            'cod_unidade'           => 'required',
            'cod_custo'             => 'required',
            'cod_gasto'             => 'required',
            'gestor_aprovador'      => 'required',
            'despesas_selecionadas' => 'required|array|min:1', // IDs das despesas vindos do checkbox
        ]);

        try {
            // Inicia a transação: ou grava tudo, ou não grava nada!
            DB::beginTransaction();

            // 1. CALCULAR O TOTAL (Novidade aqui)
            // Buscamos a soma de todos os 'valor' das despesas selecionadas
            $valorTotal = Reembolso::whereIn('id', $request->despesas_selecionadas)
                            ->where('user_id', Auth::id())
                            ->sum('valor');

            // 2. Criar o Relatório (O "Pai")
            $relatorio = Financeiro::create([
                'tipo'            => 'reembolso',
                'favorecido'           => $validated['favorecido'],
                'referencia'           => $validated['referencia'],
                'cod_unidade'      => $validated['cod_unidade'],
                'cod_custo'        => $validated['cod_custo'],
                'cod_gasto'        => $validated['cod_gasto'],
                'gestor_aprovador' => $validated['gestor_aprovador'],
                'valor'            => $valorTotal,
                'user_id'          => Auth::id(),
                'status'           => 'pendente',
                'approval_token'   => Str::uuid(),
            ]);


            // 3. Atualizar as Despesas (Os "Filhos")
            // Aqui pegamos todos os IDs que vieram no array despesas_selecionadas
            Reembolso::whereIn('id', $request->despesas_selecionadas)
                ->where('user_id', Auth::id()) // Segurança: garante que o usuário só altere as próprias despesas
                ->update([
                    'relatorio_id' => $relatorio->id,
                    'status'       => 'em_relatorio' 
                ]);

            


            $relatorio->load('unidades', 'despesas', 'user', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $relatorio->update([
                'pix' => ($relatorio->despesas->first()->pix ?? '0')
            ]);
            

            // Se chegou aqui sem erro, confirma no banco
            DB::commit();

            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.reembolso_pendente', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
                $message->to($relatorio->gestor_aprovador);
                $message->subject('Solicitação de Relatório de Reembolso Pendente - Protocolo: ' . $relatorio->id);
                

            });

            Mail::send('emails.relatorio_solicitacao', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
                $message->to($relatorio->user->email);
                $message->subject('Confirmação de Solicitação Financeiro - Protocolo: ' . $relatorio->id);


            });





            return redirect()->route('reembolso.create')->with('success', 'Relatório criado e despesas vinculadas!');

        } catch (\Exception $e) {
            // Se der qualquer erro (banco caiu, campo faltando), desfaz tudo
            DB::rollBack();
            
            return redirect()->back()
                            ->withErrors(['error' => 'Falha ao salvar relatório: ' . $e->getMessage()])
                            ->withInput();
        }
    }



    public function telaAprovacao($token)
    {
        // Busca o relatório pelo token com as despesas
        $relatorio = Financeiro::where('approval_token', $token)->firstOrFail();
        
        // Carrega as despesas vinculadas
        $despesas = $relatorio->despesas; 

        // Soma apenas as despesas que o gestor já clicou em "Aprovar"
        $valorAprovado = $despesas->where('status', 'aprovado')->sum('valor');
        
        $existePendente = $despesas->contains('status', 'pendente');

        return view('financeiro_fr.aprovacao_gestor', compact('relatorio', 'despesas', 'valorAprovado', 'existePendente'));
    }

        // No Controller de Despesas
    public function updateStatus(Request $request, $id)
    {
        $despesa = Reembolso::findOrFail($id);
        $despesa->update([
            'status' => $request->status, 
            'observacao'=> $request->observacao
        ]);

        return redirect()->back()->with('success', 'Status da despesa atualizado!');
    }

    public function finalizar(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $statusFinal = $request->input('status_final'); // 'aprovado' ou 'reprovado'

        // 1. Carregamos as relações
        $relatorio = Financeiro::with(['user','despesas', 'unidades', 'unidadeAprovadora', 'centroGasto', 'centroCusto'])
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
            
            return redirect()->back()->with('error', 'Relatório reprovado. As despesas foram desvinculadas para correção.');
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


        // 4. Atualiza o Relatório para Aprovado
        $relatorio->update([
            'valor'  => $novoValorTotal,
            'status' => 'aprovado'
        ]);
    

        $x = 0;


        // Limpeza: Desvincular despesas reprovadas (embora o bloqueio acima impeça isso de chegar aqui, é uma boa segurança)
        Reembolso::where('relatorio_id', $relatorio->id)
            ->where('status', 'reprovado')
            ->update(['relatorio_id' => null]);

        $relatorio->update([
            'id_raz' => ($fornecedorId) . '-A-' . $relatorio->id
        ]);


        $situacaoPadrao = 'D';

        $situacaoParaEnviar = $situacaoPadrao;
        $valorLiquidoParaEnviar = $relatorio->valor;
        $valorUtilizadoParaEnviar = 0;
        

        // Finalizações no Rodopar
        $relatorio->finalizar($fornecedorId, $relatorio->valor, $relatorio->id, $relatorio->cod_unidade, $relatorio->cod_custo, $relatorio->cod_gasto, $relatorio->unidadeAprovadora->nome_gestor ?? 'Gestor não definido', $valorLiquidoParaEnviar, $valorUtilizadoParaEnviar, $situacaoParaEnviar);

        foreach ($despesasAprovadas as $item) {
            $item->pagrat($fornecedorId, $item->valor, $relatorio->id, $relatorio->cod_unidade, $relatorio->cod_custo, $relatorio->cod_gasto, $item->despesa, $item->SINTET, $item->ANALIT);
        
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
            Mail::send('emails.reembolso_finalizar', [
                'relatorio' => $relatorio, 
                'zipUrl'    => $zipUrl,
                'zipName'   => $zipName
            ], function ($message) use ($relatorio) {
                $message->to('contasapagar@grupocargopolo.com.br');
                //$message->to('higor.machado@grupocargopolo.com.br');
                $message->cc([$relatorio->unidades?->email_gestor ?? null,$relatorio->user?->email ?? null]);
                $message->subject('Relatório de Reembolso Aprovado - Protocolo: ' . $relatorio->id);
            });
        } catch (\Exception $e) { }

        return redirect()->route('financeiro_fr.index')
            ->with('success', 'Relatório finalizado com sucesso! Valor: R$ ' . number_format($novoValorTotal, 2, ',', '.'));

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['error' => 'Falha ao processar relatório: ' . $e->getMessage()])
            ->withInput();
    }
}


}

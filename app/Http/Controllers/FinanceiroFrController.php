<?php

namespace App\Http\Controllers;

use App\Models\Financeiro;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Facades\Mail;

use App\Models\FornecedorFinanceiro;
use App\Models\UnidadesNegocio;
use App\Models\GestorFinanceiro;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 



class FinanceiroFrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $filiais = UnidadesNegocio::orderBy('unidade_negocio')->get(); //->pluck('filial');
        // NÃO carregue fornecedores aqui – AJAX fará isso
        $fornecedores = []; // opcional, apenas para evitar erro no Blade

        //dd($fornecedores->count(), $fornecedores->first());

        
        return view('financeiro_fr.index', compact('filiais','fornecedores'));
        //
    }

    public function buscarFornecedores(Request $request)
    {
        $search = $request->search;

        $fornecedores = FornecedorFinanceiro::fornecedoresQuery($search);

        return response()->json($fornecedores);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipo' => 'nullable|string',
            'pedido' => 'nullable|string',
            'referencia' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'name' => 'nullable|string',
            'pamcard' => 'nullable|string',
            'banco' => 'nullable|string',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'pix' => 'nullable|string',
            'favorecido' => 'nullable|string',
            'valor' => 'nullable|string',
            'motivo' => 'nullable|string',
            'filial' => 'nullable|string',
            'email' => 'required|email',
            'email_gestor' => 'required|email',
            'tipo_pix' => 'nullable|string',
            'placa' => 'nullable|string',
            'prazo' => 'nullable|string',
            'socorro_em_rota' => 'nullable|string',
            'solicitante' => 'nullable|string',
            'fornecedor' => ['required', 'string', 'not_regex:/^\s*$/'],
            'cod_unidade' => ['required', 'string', 'not_regex:/^\s*$/'],
            'cod_custo' => ['required', 'string', 'not_regex:/^\s*$/'],
            'cod_gasto' => ['required', 'string', 'not_regex:/^\s*$/'],
            'tem_nota_fiscal' => 'nullable|string',
            'gestor_aprovador' => ['required', 'string', 'not_regex:/^\s*$/'],
            'emails' => 'nullable|string',
            'anexo_path' => 'nullable|string'

        ]);

        // if($validatedData['tem_nota_fiscal'] == 'sim' && $validatedData['tipo'] == 'avista'){
        //     $situacao = DB::connection('sqlsrv')
        //         ->table('ESTPED')
        //         ->where('NUMPED', $validatedData['pedido'])
        //         ->value('SITUAC');

        //     if ($situacao !== 'A') {
        //         return back()->withErrors(['pedido' => 'O pedido informado não está com situação Aprovado!.']);
        //     }
        // }

        if($validatedData['socorro_em_rota'] == 'sim' && $validatedData['placa'] == null){
            return back()->withErrors(['placa' => 'Para socorro em rota, a placa é obrigatória!']);
        }

        // dd($validatedData);

        $financeiro = Financeiro::create(array_merge(
            $validatedData,
            [
                'approval_token' => Str::uuid(),
                'status' => 'pendente'
            ]
        ));

        // if($validatedData['tem_nota_fiscal'] == 'nao' && $validatedData['tipo'] == 'avista'){
        //     $financeiro->financeiroAvista($financeiro->id, $validatedData['valor'], $validatedData['solicitante'], $validatedData['fornecedor'], $validatedData['pedido'],$validatedData['placa'],$validatedData['prazo'], $validatedData['cod_unidade']);
        //     $financeiro->update(['id_raz' => DB::connection('sqlsrv')->table('BANRAZ')->max('ID_RAZ')]);
        // } else{
        //     $financeiro->pagdoc($validatedData['fornecedor'], $validatedData['valor'], $financeiro->id, $validatedData['cod_unidade'], $validatedData['cod_custo'], $validatedData['cod_gasto']);

        // }

        if($validatedData['tipo'] == 'avista'){

            $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $gestor = $financeiro->unidadeAprovadora?->gestorRegional;

            if (!$gestor) {
                return back()->withErrors('Gestor regional não encontrado para esta unidade.');
            }

            $calculo = $gestor->saldo - $financeiro->valor;

            if ($calculo < 0) {
                return back()->withErrors('Saldo insuficiente do gestor para aprovar esta solicitação.');
            } else {
                $gestor->saldo = $calculo;
                $gestor->save();
            }
        }


        // ENVIAR VÁRIOS E-MAILS

        $emailsString = $request->input('emails');

        $emailsValidos = []; // inicializa como array vazio

        if (!empty($emailsString)) {

            // Divide a string em array usando ';' como separador
            $emailsArray = array_map('trim', explode(';', $emailsString));

            // Filtra apenas e-mails válidos
            $emailsValidos = array_filter($emailsArray, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            // Opcional: retornar erro se algum e-mail for inválido
            if (count($emailsValidos) !== count($emailsArray)) {
                return back()->withErrors(['emails' => 'Um ou mais e-mails são inválidos.']);
            }
        }
        //FIM

       

        $emails = array_filter([
            ...$emailsValidos,
            $validatedData['email'],
            $validatedData['email_gestor'],
            $validatedData['tipo'] === 'adiantamento' ? 'vitor.marques@grupocargopolo.com.br' : null,
        ], function ($email) {
            return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        });

        //dd($validatedData['tipo']);

        $foto = $request->file('foto');
        $nota_fiscal = $request->file('nota_fiscal');

        // TRATAMENTO DO UPLOAD DO ANEXO
        // STORE
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $file = $request->file('foto');
            $extension = $file->guessExtension() ?? 'bin';
            $filename = time() . '_' . Str::uuid() . '.' . $extension;

            $file->move(storage_path('app/public/financeiro'), $filename);

            $path = 'financeiro/' . $filename;
            $financeiro->update(['anexo_path' => $path]);


            $financeiro->update(['anexo_path' => $path]);
        }
        // FIM ANEXO


        $tipos = $request->input('tipo_reembolso', []);

        $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');


        // dd($tipos);

        if($validatedData['tipo'] == 'avista'){

            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.financeiro_pendente', ['financeiro' => $financeiro], function ($message) use ($validatedData, $financeiro) {
                $message->to($validatedData['gestor_aprovador']);
                if($validatedData['socorro_em_rota'] == 'sim'){
                    $message->subject('Solicitação de Socorro em Rota Pendente - ' . $validatedData['tipo'] . ' - Protocolo: ' . $financeiro->id);
                } else {
                    $message->subject('Solicitação Financeiro Pendente - ' . $validatedData['tipo'] . ' - Protocolo: ' . $financeiro->id);
                }
                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($financeiro->anexo_path) && Storage::disk('public')->exists($financeiro->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $financeiro->anexo_path));
                }

            });

            Mail::send('emails.financeiro_solicitacao', ['financeiro' => $financeiro], function ($message) use ($validatedData, $financeiro) {
                $message->to($validatedData['email']);
                $message->subject('Confirmação de Solicitação Financeiro - ' . $validatedData['tipo'] . ' - Protocolo: ' . $financeiro->id);

                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($financeiro->anexo_path) && Storage::disk('public')->exists($financeiro->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $financeiro->anexo_path));
                }

            });
            return redirect()->route('financeiro_fr.index')->with('success', 'Solicitação realizada com sucesso!');
        }



        if ($validatedData['tipo'] == 'adiantamento' || $validatedData['tipo'] == 'adiantamento'){
            // Envia o email com os dados do formulário
            Mail::send('emails.financeiro', ['dados' => $validatedData, 'financeiro' => $financeiro,], function($message) use ($validatedData, $foto, $financeiro, $emails){
                //$message->to(['contasapagar@grupocargopolo.com.br']);
                $message->to('higor.05@hotmail.com');
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                if($validatedData['tipo'] == 'adiantamento'){
                    $message->cc($emails);
                } else {
                    $message->cc($emails);
                }
                if($validatedData['tipo'] == 'avista' && $validatedData['pedido'] != null){ 
                    $message->subject( 'PAGAMENTO A VISTA; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );
                } elseif ($validatedData['tipo'] == 'avista' && $validatedData['pedido'] == null){
                    $message->subject( 'PAGAMENTO A VISTA; PROTOCOLO:'. $financeiro->id  . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );
                } elseif ($validatedData['tipo'] == 'adiantamento' && $validatedData['pedido'] != null){
                    $message->subject( 'ADIANTAMENTO À FORNECEDOR; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] );
                } elseif ($validatedData['tipo'] == 'adiantamento' && $validatedData['pedido'] == null){
                    $message->subject( 'ADIANTAMENTO À FORNECEDOR; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] );
                }

                //Verificar se existe imagem anexada
                if ($foto)  {
                    $pathToFile = $foto->getPathname();
                    $filename = $foto->getClientOriginalName();
                    $message->attach($pathToFile, [
                        'as' => $filename, // Nome do arquivo que será mostrado no email
                        'mime' => $foto->getClientMimeType(), // Tipo MIME do arquivo
                    ]);
                }

                if ($foto) {
                    \Log::info('Foto anexada: ' . $foto->getClientOriginalName());
                } else {
                    \Log::info('Foto não anexada.');
                }
                

            });
        } else {
                        // Envia o email com os dados do formulário
                        Mail::send('emails.financeiro_reembolso', ['dados' => $validatedData, 'financeiro' => $financeiro, 'tipos' => $tipos,], function($message) use ($validatedData, $foto, $nota_fiscal,$financeiro,$emails){
                            $message->to(['contasapagar@grupocargopolo.com.br']);
                            // $message->to('higor.05@hotmail.com');
                            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                            $message->cc($emails);
                            $message->subject( 'DESPESAS/REEMBOLSO; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );

                            if ($nota_fiscal) {
                                $pathToFile = $nota_fiscal->getPathname();
                                $filename = $nota_fiscal->getClientOriginalName();
                                $message->attach($pathToFile, [
                                    'as' => $filename,
                                    'mime' => $nota_fiscal->getClientMimeType(),
                                ]);
                            }

                            //Verificar se existe imagem anexada
                            if ($foto)  {
                                $pathToFile = $foto->getPathname();
                                $filename = $foto->getClientOriginalName();
                                $message->attach($pathToFile, [
                                    'as' => $filename, // Nome do arquivo que será mostrado no email
                                    'mime' => $foto->getClientMimeType(), // Tipo MIME do arquivo
                                ]);
                            }
            
                            if ($foto) {
                                \Log::info('Foto anexada: ' . $foto->getClientOriginalName());
                            } else {
                                \Log::info('Foto não anexada.');
                            }
                            
            
                        });
        }
        
        

        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('financeiro_fr.index')->with('success', 'Solicitação realizada com sucesso!');
    }

        // APROVAR - gestor clicou no link -> dispara para os setores
    public function aprovar($token)
    {
        $financeiro = Financeiro::where('approval_token', $token)->firstOrFail();

        if ($financeiro->status !== 'pendente') {
            return back()->withErrors('Solicitação já foi processada.');
        }

        // ENVIAR VÁRIOS E-MAILS

        $emailsString = $financeiro->emails;

        $emailsValidos = []; // inicializa como array vazio

        if (!empty($emailsString)) {

            // Divide a string em array usando ';' como separador
            $emailsArray = array_map('trim', explode(';', $emailsString));

            // Filtra apenas e-mails válidos
            $emailsValidos = array_filter($emailsArray, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            // Opcional: retornar erro se algum e-mail for inválido
            if (count($emailsValidos) !== count($emailsArray)) {
                return back()->withErrors(['emails' => 'Um ou mais e-mails são inválidos.']);
            }
        }

        $emails = array_filter([
            ...$emailsValidos,
            $financeiro->email,
            $financeiro->email_gestor,
            $financeiro->gestor_aprovador,
            $financeiro->unidadeAprovadora?->gestorRegional?->email_gestor
        ], function ($email) {
            return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        });

        //FIM

        $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

        
        if($financeiro->tem_nota_fiscal == 'sim' && $financeiro->tipo == 'avista'){
            $financeiro->update(['id_raz' => DB::connection('sqlsrv')->table('BANRAZ')->max('ID_RAZ') + 1]);
        } else {
            $financeiro->update(['id_raz' => $financeiro->fornecedor. '-A-' . $financeiro->fornecedor .'-'. $financeiro->id]);
        }

        // Decide qual e-mail disparar pelo tipo
        if ($financeiro->tipo == 'avista') {
            Mail::send('emails.financeiro_avista', [
                'financeiro' => $financeiro,
            ], function($message) use ($financeiro,$emails){
                $message->to('contasapagar@grupocargopolo.com.br');
                //$message->to('higor.05@hotmail.com');
                $message->cc($emails);

                if ($financeiro->tipo == 'avista' && ($financeiro->socorro_em_rota == 'nao') && ( $financeiro->pedido == '000000' || $financeiro->pedido == null )){
                    $message->subject( 'PAGAMENTO A VISTA; PROTOCOLO:'. $financeiro->id  . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $financeiro->placa );
                } elseif ($financeiro->tipo == 'avista' && ($financeiro->socorro_em_rota == 'nao') &&  $financeiro->pedido != null) {
                    $message->subject( 'PAGAMENTO A VISTA; PEDIDO: '. $financeiro->pedido . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $financeiro->placa );
                } elseif($financeiro->socorro_em_rota == 'sim'){
                    $message->subject( 'SOCORRO EM ROTA; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $financeiro->placa );
                }
            
            if (!empty($financeiro->anexo_path) && Storage::disk('public')->exists($financeiro->anexo_path)) {
                $message->attach(storage_path('app/public/' . $financeiro->anexo_path));
            }
    }
        );
        }

        if($financeiro->tem_nota_fiscal == 'sim' && $financeiro->tipo == 'avista'){
            $financeiro->financeiroAvista($financeiro->id, $financeiro->valor, $financeiro->solicitante, $financeiro->fornecedor, $financeiro->pedido,$financeiro->placa,$financeiro->unidadeAprovadora->nome_gestor, $financeiro->cod_unidade, $financeiro->unidades->conta);
            //$financeiro->update(['id_raz' => DB::connection('sqlsrv')->table('BANRAZ')->max('ID_RAZ')]);
        } else{
            $financeiro->pagdoc($financeiro->fornecedor, $financeiro->valor, $financeiro->id, $financeiro->cod_unidade, $financeiro->cod_custo, $financeiro->cod_gasto,$financeiro->unidadeAprovadora->nome_gestor);

        }

        $financeiro->update(['status' => 'aprovado']);

        return back()->with('aprovado','Solicitação aprovada com sucesso!');
    }



    public function reprovar($token)
    {
        $financeiro = Financeiro::where('approval_token', $token)->firstOrFail();

        if ($financeiro->status !== 'pendente') {
            return back()->withErrors('Solicitação já foi processada.');
        }

        $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

        Mail::send('emails.financeiro_reprovado', ['financeiro' => $financeiro], function($message) use ($financeiro){
            $message->to($financeiro->email);
            $message->cc($financeiro->email_gestor,$financeiro->gestor_aprovador);
            $message->subject('Solicitação Reprovada - Protocolo: ' . $financeiro->id);
        });

        $gestor = $financeiro->unidadeAprovadora?->gestorRegional;

        if (!$gestor) {
            return back()->withErrors('Gestor regional não encontrado para esta unidade.');
        }

        $calculo = $gestor->saldo + $financeiro->valor;

        $gestor->saldo = $calculo;
        $gestor->save();

        $financeiro->update(['status' => 'reprovado']);

        return back()->withErrors('Solicitação reprovada com sucesso! Seu saldo foi atualizado.');
    }


    /**
     * Display the specified resource.
     */
    public function saldo()
    {
        $saldo = GestorFinanceiro::get();

        $saldo->load('unidade');

        $unidades = UnidadesNegocio::orderBy('unidade_negocio')->get();

        return view('financeiro_fr.saldos', compact('saldo','unidades'));
        //
    }

    public function update(Request $request)
    {
        $saldo = GestorFinanceiro::find($request->id);

        if (!$saldo) {
            return response()->json(['success' => false, 'message' => 'Registro não encontrado']);
        }

        $saldo->saldo = $request->saldo;
        $saldo->save();

        return response()->json([
            'success' => true,
            'novo_saldo' => $saldo->saldo,
            'atualizado' => $saldo->updated_at->format('d/m/Y H:i:s')
        ]);
    }

    public function update_gestor(Request $request)
{
    try {

        $request->validate([
            'id'    => 'required|integer',
            'campo' => 'required|string|in:nome,email',
            'valor' => 'required|string'
        ]);

        // 🔍 Primeiro busca na tabela UNIDADES DE NEGÓCIO
        $unidade = UnidadesNegocio::find($request->id);

        if (!$unidade) {
            return response()->json([
                'success' => false,
                'message' => 'Unidade não encontrada'
            ]);
        }

        // -------------------------------------
        //  ATUALIZA NOME
        // -------------------------------------
        if ($request->campo === "nome") {
            $valorFormatado = strtoupper($request->valor); // obrigar MAIÚSCULAS
            $unidade->nome_gestor = $valorFormatado;
        }

        // -------------------------------------
        //  ATUALIZA EMAIL
        // -------------------------------------
        if ($request->campo === "email") {

            if (!filter_var($request->valor, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail inválido'
                ]);
            }

            $unidade->email_gestor = strtolower($request->valor);
        }

        // Salva atualização da UNIDADE
        $unidade->save();


        // -----------------------------------------------------------
        //  🔄 SINCRONIZA automaticamente com a tabela GestoresFinanceiro
        //  usando WHERE cod_unidade (pois você mostrou isso acima)
        // -----------------------------------------------------------
        // \DB::table('gestores_financeiro')
        //     ->where('cod_unidade', $unidade->cod_unidade)
        //     ->update([
        //         'nome_gestor' => $unidade->nome_gestor,
        //         'email_gestor' => $unidade->email_gestor,
        //         'updated_at'   => now()
        //     ]);


        return response()->json([
            'success'   => true,
            'valor'     => $request->campo === "nome" ? $unidade->nome_gestor : $unidade->email_gestor,
            'atualizado'=> now()->format('d/m/Y H:i:s')
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Erro interno: '.$e->getMessage()
        ], 500);
    }
}

    public function update_conta(Request $request)
    {
        $conta = UnidadesNegocio::find($request->id);

        if (!$conta) {
            return response()->json(['success' => false, 'message' => 'Registro não encontrado']);
        }

        $conta->conta = $request->conta;
        $conta->save();

        return response()->json([
            'success' => true,
            'novo_conta' => $conta->conta,
            'atualizado' => $conta->updated_at->format('d/m/Y H:i:s')
        ]);
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Financeiro $financeiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Financeiro $financeiro)
    {
        //
    }

public function dashboard(Request $request)
    {
        // --- 1. Filtro de Data ---
        $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate'))->startOfDay() : Carbon::now()->startOfYear();
        $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate'))->endOfDay() : Carbon::now()->endOfYear();

        // --- NOVO: Captura o filtro de Tipo ---
        $selectedTipo = $request->input('tipo');

        $query = Financeiro::query()->whereBetween('created_at', [$startDate, $endDate]);

        // --- NOVO: Aplica o filtro de Tipo se estiver presente ---
        if ($selectedTipo) {
            $query->where('tipo', $selectedTipo);
        }

        // 🚨 CHAME AGORA USANDO $this->cleanMoneyValue() 🚨
        $cleanSql = $this->cleanMoneyValue('valor');

        // O valor a ser comparado (500 mil)
        $limite = 500000;

        // APLICAÇÃO DO FILTRO DE VALOR (menor que 500k)
        $query->whereRaw("{$cleanSql} < ?", [$limite]);

        // -----------------------------------------------------------------

        // --- 2. Métricas Principais (KPIs) ---
        
        // CALCULA O VALOR TOTAL USANDO A LIMPEZA
        $valorTotal = (clone $query)
            ->select(DB::raw("SUM({$cleanSql}) as total_sum"))
            ->first()
            ->total_sum ?? 0;


        $totalPedidos = (clone $query)->count('id');
        $valorMedio = $totalPedidos > 0 ? $valorTotal / $totalPedidos : 0;

        $valorTotalFormatado = $this->formatBigNumber($valorTotal);
        $valorMedioFormatado = $this->formatBigNumber($valorMedio);

        // --- 3. Análise de Prazo --- (REMOVIDO CONFORME SOLICITADO)
        // Removendo $atrasados e $noPrazo daqui.
        
        
        // --- 4. Dados para Gráficos ---
        // A. Movimentação Mensal
        $movimentacaoMensal = (clone $query)
            ->select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw("SUM({$cleanSql}) as total_mes") 
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
            
        $meses = $movimentacaoMensal->pluck('mes')->map(fn($m) => Carbon::create(null, $m, 1)->translatedFormat('M'))->toArray();
        // APLICAR number_format AQUI:
        $totaisMensais = $movimentacaoMensal->pluck('total_mes')->map(fn($v) => number_format($v, 2, '.', ''))->toArray(); 


        // B. Top 5 Filiais por Valor
        $topFiliais = (clone $query)
            ->select('filial', DB::raw("SUM({$cleanSql}) as total_filial")) 
            ->groupBy('filial')
            ->orderByDesc('total_filial')
            ->take(5)
            ->get();
            
        $filiaisLabels = $topFiliais->pluck('filial')->toArray();
        // APLICAR number_format AQUI:
        $filiaisTotais = $topFiliais->pluck('total_filial')->map(fn($v) => number_format($v, 2, '.', ''))->toArray();

        // C. Distribuição por Tipo (Não usa 'valor', está OK)
        $distribuicaoTipo = (clone $query)
            ->select('tipo', DB::raw('COUNT(*) as contagem'))
            ->groupBy('tipo')
            ->get();
            
        $tiposLabels = $distribuicaoTipo->pluck('tipo')->toArray();
        $tiposContagem = $distribuicaoTipo->pluck('contagem')->toArray();

        // --- D. NOVO GRÁFICO: Tendência do Valor Médio Mensal ---
        $valorMedioMensalData = (clone $query)
            ->select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw("COUNT(*) as total_pedidos"),
                DB::raw("SUM({$cleanSql}) as total_valor") // Usa o valor limpo
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $valorMedioMensal = $valorMedioMensalData->map(function ($item) {
            $valorMedioBruto = $item->total_pedidos > 0 ? $item->total_valor / $item->total_pedidos : 0;
            // APLICAR number_format AQUI:
            return number_format($valorMedioBruto, 2, '.', '');
        })->toArray();
        
        // --- E. NOVO GRÁFICO: Distribuição de Pedidos por Ano ---
        $contagemAnualData = (clone $query)
            ->select(
                DB::raw('YEAR(created_at) as ano'),
                DB::raw('COUNT(*) as contagem')
            )
            ->groupBy('ano')
            ->orderBy('ano')
            ->get();
            
        $anos = $contagemAnualData->pluck('ano')->toArray();
        $contagemAnual = $contagemAnualData->pluck('contagem')->toArray();


        // --- 5. Retorno para a View ---
        return view('admin.financeiro-dashboard', compact(
            'valorTotal', 
            'totalPedidos', 
            'valorMedio',
            // 'atrasados', // REMOVIDO
            // 'noPrazo',   // REMOVIDO
            'meses', 
            'totaisMensais',
            'filiaisLabels',
            'filiaisTotais',
            'tiposLabels',
            'tiposContagem',
            'startDate', 
            'endDate',
            'valorTotalFormatado',
            'valorMedioFormatado',
            'selectedTipo',
            'valorMedioMensal', // NOVO DADO
            'anos',             // NOVO DADO
            'contagemAnual'     // NOVO DADO
        ));
    }

    // Dentro da classe FinanceiroController, adicione este método privado:
private function formatBigNumber(float $number): string
{
    // Array com os sufixos e seus respectivos valores
    $units = [
        1000000000000 => 'T', // Trilhão
        1000000000 => 'B',  // Bilhão
        1000000 => 'M',     // Milhão
        1000 => 'K',        // Mil
    ];

    // Verifica se o número é zero
    if ($number == 0) {
        return '0';
    }

    // Garante que o número seja positivo para o cálculo
    $absNumber = abs($number);

    // Itera sobre as unidades de maior para menor
    foreach ($units as $unit => $suffix) {
        if ($absNumber >= $unit) {
            // Calcula o valor formatado com 2 casas decimais
            $formattedValue = number_format($number / $unit, 2, ',', '.');
            
            // Retorna o valor com o sufixo
            return 'R$ ' . $formattedValue . $suffix;
        }
    }

    // Se for menor que mil, retorna o número normal formatado
    return 'R$ ' . number_format($number, 2, ',', '.');
}

// Você pode criar um Helper ou colocar esta função dentro do seu Model ou Controller temporariamente

private function cleanMoneyValue(string $columnName): string
{
    // Regex: [^0-9,] significa "qualquer caractere que NÃO seja (^) um número (0-9) OU uma vírgula (,)".
    // Substituímos tudo o que for diferente de número ou vírgula por uma string vazia ('').
    $removeSymbols = "REGEXP_REPLACE({$columnName}, '[^0-9,]', '')";

    // Em seguida, substituímos a vírgula restante pelo ponto decimal, preparando para a soma.
    $cleanedValue = "REPLACE({$removeSymbols}, ',', '.')";
    
    return $cleanedValue;
}

}

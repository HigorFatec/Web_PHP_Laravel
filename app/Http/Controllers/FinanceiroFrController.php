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
use ZipArchive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Rdv;
use Illuminate\Support\Facades\Cache; // Não esqueça do import
use Illuminate\Support\Facades\Log;




class FinanceiroFrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filiais = UnidadesNegocio::orderBy('unidade_negocio')->get(); //->pluck('filial');

        // $filiais = DB::connection('sqlsrv')->table('RODUNN')
        //     ->select('CODUNN', 'DESCRI')
        //     ->orderBy('DESCRI')
        //     ->get();

        $unidade = Financeiro::unidade_de_negocio();
        $custo = Financeiro::centro_de_custo();
        $gasto = Financeiro::centro_de_gasto();



        // NÃO carregue fornecedores aqui – AJAX fará isso
        $fornecedores = []; // opcional, apenas para evitar erro no Blade

        //dd($fornecedores->count(), $fornecedores->first());
    
        return view('financeiro_fr.index', compact('filiais','fornecedores','unidade','gasto','custo'));
        //
    }


    public function formReprovar($id)
    {
        $r = Financeiro::findOrFail($id);

        
        return view('financeiro_fr.reprovar', compact('r'));
    }

    public function resumo(Request $request)
    {
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        if ($user->temSetor(['financeiro','admin','suprimentos'])){

            $financeiro = Financeiro::where('status', 'aprovado_gestor')
            ->orWhere(function ($query) {
                $query->where('status', 'aprovado')
                    ->where('tipo', 'reembolso');
            })
            ->orderBy('created_at', 'desc')->paginate(500);

            $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $finalizados = Financeiro::where('status', 'finalizado')->orderBy('created_at', 'desc')->paginate(500);

            $status_pix_fr = \Illuminate\Support\Facades\DB::table('configuracoes_sistema')
                    ->where('chave', 'status_pix_fr')
                    ->value('valor') ?? 0;

            return view('financeiro_fr.resumo', compact('financeiro', 'finalizados', 'status_pix_fr'));

        } else {
            $financeiro = Financeiro::where(function($query) {
                    // Bloco 1: Regra de Permissão (Usuário ou Gestor)
                    $query->where('user_id', auth()->id())
                        ->orWhere('gestor_aprovador', auth()->user()?->email);
                })
                ->where(function($query) {
                    // Bloco 2: Regra de Status e Tipo
                    $query->whereIn('status', ['aprovado_gestor', 'pendente'])
                        ->orWhere(function($subQuery) {
                            $subQuery->where('status', 'aprovado')
                                    ->where('tipo', 'reembolso');
                        });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(500);

            $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $finalizados = Financeiro::where(function($query) {
                    $query->where('user_id', auth()->id());
                })
                ->where('status', 'finalizado')
                ->orderBy('created_at', 'desc')
                ->paginate(500);

            return view('financeiro_fr.resumo', compact('financeiro', 'finalizados'));

        }

    }

    
public function exibirBI()
{
    $user = auth()->user();

    if (!$user || !$user->temSetor(['financeiro', 'admin'])) {
        abort(403, 'Acesso negado.');
    }

    try {
        // 1. Tenta pegar o Access Token do Cache (evita o bloqueio que o Django sofreu)
        $accessToken = Cache::remember('pbi_access_token', 3000, function () {
            $response = Http::asForm()->post("https://login.microsoftonline.com/" . env('POWERBI_TENANT_ID') . "/oauth2/v2.0/token", [
                'grant_type'    => 'password',
                'client_id'     => env('POWERBI_CLIENT_ID'),
                'client_secret' => env('POWERBI_CLIENT_SECRET'),
                'username'      => env('POWERBI_USERNAME'),
                'password'      => env('POWERBI_PASSWORD'),
                'scope'         => 'https://analysis.windows.net/powerbi/api/.default'
            ]);

            if ($response->failed()) {
                throw new \Exception('Falha na autenticação Master User');
            }

            return $response->json()['access_token'];
        });

        // 2. Gerar o Embed Token (Este também pode ter um cache curto se o ReportID for fixo)
        $groupId  = env('POWERBI_GROUP_ID');
        $reportId = env('POWERBI_REPORT_ID');

        // Dica: Se o dashboard é o mesmo para todos, faça cache aqui também!
        $embedData = Cache::remember("pbi_embed_token_{$reportId}", 3000, function () use ($accessToken, $groupId, $reportId) {
            $response = Http::withToken($accessToken)
                ->post("https://api.powerbi.com/v1.0/myorg/groups/$groupId/reports/$reportId/GenerateToken", [
                    'accessLevel' => 'view'
                ]);

            if ($response->failed()) {
                throw new \Exception('Falha ao gerar Embed Token');
            }

            return [
                'token' => $response->json()['token'],
                'url'   => "https://app.powerbi.com/reportEmbed?reportId=$reportId&groupId=$groupId"
            ];
        });

        $embedToken = $embedData['token'];
        $embedUrl   = $embedData['url'];

        // Verifica se a chave existe no cache antes de retornar a view
        $veioDoCache = Cache::has('pbi_access_token') ? 'Sim (Otimizado)' : 'Não (Primeira carga)';

        return view('financeiro_fr.bi', compact('embedToken', 'embedUrl', 'reportId', 'veioDoCache'));

    } catch (\Exception $e) {
        return "Erro no BI: " . $e->getMessage();
    }
}



    public function indexFinalizados(Request $request)
{
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        if ($user->temSetor(['financeiro','admin','suprimentos'])){

            $financeiro = Financeiro::where('status', 'aprovado_gestor')->orderBy('created_at', 'desc')->paginate(500);

            $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $finalizados = Financeiro::where('status', 'finalizado')->orderBy('updated_at', 'desc')->paginate(500);

            return view('financeiro_fr.finalizados', compact('financeiro', 'finalizados'));

        } else {
            $financeiro = Financeiro::where('status', 'aprovado_gestor')->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(500);

            $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $finalizados = Financeiro::where(function($query) {
                    $query->where('user_id', auth()?->id())
                        ->orWhere('gestor_aprovador', auth()->user()?->email);
                })
                ->where('status', 'finalizado')
                ->orderBy('updated_at', 'desc')
                ->paginate(500);



            return view('financeiro_fr.finalizados', compact('financeiro', 'finalizados'));

        }

        

    }

    public function indexPendentes(Request $request)
    {
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        if ($user->temSetor(['financeiro','admin'])){

            $pendentes = Financeiro::where('status', 'aprovado_gestor')->orderBy('created_at', 'desc')->get();

            $pendentes->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            return view('financeiro_fr.pendentes', compact('pendentes'));

        } else {
            $pendentes = Financeiro::where('status', 'aprovado_gestor')->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

            $pendentes->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');



            return view('financeiro_fr.finalizados', compact('financeiro', 'finalizados'));

        }

    }






    public function updateFiscalStatus(Request $request, $id)
    {
        // Validação básica
        $request->validate([
            'tem_nota_fiscal' => 'required|in:sim,nao'
        ]);

        try {
            // Aqui usamos o seu modelo (provavelmente Relatorio ou Reserva)
            // Ajuste "Financeiro" para o nome correto do seu Model
            $registro = \App\Models\Financeiro::findOrFail($id); 
            $registro->tem_nota_fiscal = $request->tem_nota_fiscal;
            $registro->save();

            return response()->json([
                'success' => true, 
                'message' => 'Status atualizado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Erro ao atualizar.'
            ], 500);
        }
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
        if ($request->has('valor')) {
            $request->merge([
                'valor' => str_replace(',', '.', $request->valor)
            ]);
        }

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
            'valor' => 'nullable|numeric',
            'motivo' => 'nullable|string',
            'filial' => 'nullable|string',
            'email' => 'required|email',
            'email_gestor' => 'required|email',
            'tipo_pix' => 'nullable|string',
            'placa' => 'nullable|string',
            'prazo' => 'nullable|string',
            'socorro_em_rota' => 'nullable|string',
            'solicitante' => 'nullable|string',
            'fornecedor' => ['required_if:tipo,avista', 'nullable', 'string', 'not_regex:/^\s*$/'],
            'cod_unidade' => ['required', 'string', 'not_regex:/^\s*$/'],
            'cod_custo' => ['required', 'string', 'not_regex:/^\s*$/'],
            'cod_gasto' => ['required', 'string', 'not_regex:/^\s*$/'],
            'tem_nota_fiscal' => 'nullable|string',
            'gestor_aprovador' => ['required', 'string', 'not_regex:/^\s*$/'],
            'emails' => 'nullable|string',
            'anexo_path' => 'nullable|string',
            'adiantamento_fornecedor' => 'nullable|string',
            'frota_bloqueada' => 'nullable|string',

        ],[
            'valor.numeric' => 'O campo valor deve ser um número válido (ex: 1250.50).',
        ]);


        if ($validatedData['tipo'] == 'avista') {
            if($validatedData['tem_nota_fiscal'] == 'sim'){
            // Buscamos a situação do pedido
            $situacao = DB::connection('sqlsrv')
                ->table('ESTPED')
                ->where('NUMPED', $validatedData['pedido'])
                ->value('SITUAC');

            // 1. Verifica se o pedido sequer existe (se $situacao for null, o pedido não foi encontrado)
            if (is_null($situacao)) {
                return back()->withErrors(['pedido' => 'O número do pedido informado não existe no sistema.']);
            }
            // if ($situacao !== 'A' && $situacao !== 'X') {
            if ($situacao !== 'A' ) {
                return back()->withErrors(['pedido' => 'O pedido informado não está na situação aprovado.']);
            }

            $valor = DB::connection('sqlsrv')
                ->table('ESTPED')
                ->where('NUMPED', $validatedData['pedido'])
                ->value('VLRTOT');

            if ($valor <= 0) {
                return back()->withErrors(['pedido' => 'O valor total do pedido é zero ou negativo, não é possível processar.']);
            }

            // $centro_de_gasto = DB::connection('sqlsrv')
            //     ->table('PEDRAT')
            //     ->where('NUMPED', $validatedData['pedido'])
            //     ->value('CODCGA');

            // $descricao_gasto = DB::connection('sqlsrv')
            //     ->table('RODCGA')
            //     ->where('CODCGA', $centro_de_gasto)
            //     ->value('DESCRI');
            
            // if ($centro_de_gasto != $validatedData['cod_gasto']) {
            //     return back()->withErrors(['cod_gasto' => 'O centro de gasto informado não corresponde ao centro de gasto do pedido: '. $descricao_gasto]);
            // }

            // $centro_de_custo = DB::connection('sqlsrv')
            //     ->table('PEDRAT')
            //     ->where('NUMPED', $validatedData['pedido'])
            //     ->value('CODCUS');
            
            // $descricao_custo = DB::connection('sqlsrv')
            //     ->table('RODCUS')
            //     ->where('CODCUS', $centro_de_custo)
            //     ->value('DESCRI');
            
            // if ($centro_de_custo != $validatedData['cod_custo']) {
            //     return back()->withErrors(['cod_custo' => 'O centro de custo informado não corresponde ao centro de custo do pedido: '. $descricao_custo]);
            // }

            // $unidade_negocio = DB::connection('sqlsrv')
            //     ->table('PEDRAT')
            //     ->where('NUMPED', $validatedData['pedido'])
            //     ->value('CODUNN');
            
            // $descricao_unidade = DB::connection('sqlsrv')
            //     ->table('RODUNN')
            //     ->where('CODUNN', $unidade_negocio)
            //     ->value('DESCRI'); 
            
            // if ($unidade_negocio != $validatedData['cod_unidade']) {
            //     return back()->withErrors(['cod_unidade' => 'A unidade de negócio informada não corresponde à unidade de negócio do pedido: '. $descricao_unidade]);
            // }
            }
        }


        if($validatedData['tipo'] == 'avista'){
            if($validatedData['socorro_em_rota'] == 'sim' && $validatedData['placa'] == null){
                return back()->withErrors(['placa' => 'Para socorro em rota, a placa é obrigatória!']);
            }
        }
        //Obter usuário autenticado
        $user = Auth::user();

        // dd($validatedData);

        $financeiro = Financeiro::create(array_merge(
            $validatedData,
            [
                'approval_token' => Str::uuid(),
                'user_id' => $user->id,
                // 'status' => 'pendente'
                'status' => ($validatedData['tipo'] === 'avista') ? 'pendente' : ($validatedData['status'] ?? null),

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
        if($validatedData['tipo'] == 'avista'){

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
        }

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
                $message->to(['contasapagar@grupocargopolo.com.br']);
                //$message->to('higor.05@hotmail.com');
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                if($validatedData['tipo'] == 'adiantamento'){
                    $message->cc($emails);
                } else {
                    $message->cc($emails);
                }
                if($validatedData['tipo'] == 'avista' && $validatedData['pedido'] != null){ 
                    $message->subject( 'PAGAMENTO A VISTA; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $validatedData['placa'] );
                } elseif ($validatedData['tipo'] == 'avista' && $validatedData['pedido'] == null){
                    $message->subject( 'PAGAMENTO A VISTA; PROTOCOLO:'. $financeiro->id  . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $validatedData['placa'] );
                } elseif ($validatedData['tipo'] == 'adiantamento' && $validatedData['pedido'] != null){
                    $message->subject( 'ADIANTAMENTO À FORNECEDOR; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $financeiro->unidades->unidade_negocio );
                } elseif ($validatedData['tipo'] == 'adiantamento' && $validatedData['pedido'] == null){
                    $message->subject( 'ADIANTAMENTO À FORNECEDOR; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $financeiro->unidades->unidade_negocio );
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
                            //$message->cc($emails);
                            $message->subject( 'DESPESAS/REEMBOLSO; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $validatedData['placa'] );

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


public function dispararEmailsAtrasados()
{
    // 1. Busca todos os registros pendentes criados após a data específica
    $solicitacoes = Financeiro::where('created_at', '>', '2026-03-23 10:26:00')
        ->get();

    if ($solicitacoes->isEmpty()) {
        return "Nenhum registro pendente encontrado para este período.";
    }

    $contagem = 0;

    foreach ($solicitacoes as $financeiro) {
        // 2. Lógica para capturar e validar os e-mails (igual à sua)
        $emailsValidos = [];
        if (!empty($financeiro->emails)) {
            $emailsArray = array_map('trim', explode(';', $financeiro->emails));
            $emailsValidos = array_filter($emailsArray, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        }

        $destinatarios = array_filter([
            ...$emailsValidos,
            $financeiro->email,
            $financeiro->email_gestor,
            $financeiro->gestor_aprovador,
            $financeiro->unidadeAprovadora?->gestorRegional?->email_gestor
        ], function ($email) {
            return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        });

        if (empty($destinatarios)) continue;

        // 3. Carrega as relações necessárias para o template do e-mail
        $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

        // 4. Dispara o e-mail (apenas se for avista, conforme sua lógica)
        if ($financeiro->tipo == 'avista') {
            Mail::send('emails.financeiro_avista', ['financeiro' => $financeiro], function($message) use ($financeiro, $destinatarios) {
                $message->to($destinatarios);
                
                // Define o Assunto dinamicamente
                $assunto = "PAGAMENTO A VISTA; PROTOCOLO: {$financeiro->id}";
                if ($financeiro->socorro_em_rota == 'sim') $assunto = "SOCORRO EM ROTA; PROTOCOLO: {$financeiro->id}";
                
                $message->subject($assunto . " - FORNECEDOR: " . $financeiro->name);

                if (!empty($financeiro->anexo_path) && Storage::disk('public')->exists($financeiro->anexo_path)) {
                    $message->attach(storage_path('app/public/' . $financeiro->anexo_path));
                }
            });

            // 5. Atualiza o status para não enviar duplicado se rodar de novo
            // $financeiro->update(['status' => 'aprovado_gestor']);
            $contagem++;
        }
    }

    return "Processo concluído. {$contagem} solicitações foram aprovadas e e-mails enviados.";
}



    public function aprovar($token)
    {
        $financeiro = Financeiro::where('approval_token', $token)->firstOrFail();

        if ($financeiro->status !== 'pendente') {
            return 'Solicitação já foi processada.';
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

        

        // Decide qual e-mail disparar pelo tipo
        if ($financeiro->tipo == 'avista') {
            Mail::send('emails.financeiro_avista', [
                'financeiro' => $financeiro,
            ], function($message) use ($financeiro,$emails){
                //$message->to('contasapagar@grupocargopolo.com.br');
                //$message->to('higor.machado@grupocargopolo.com.br');
                $message->to($emails);

                if ($financeiro->tipo == 'avista' && ($financeiro->socorro_em_rota == 'nao') && ( $financeiro->pedido == '000000' || $financeiro->pedido == null )){
                    $message->subject( 'PAGAMENTO A VISTA; PROTOCOLO:'. $financeiro->id  . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $financeiro->placa );
                } elseif ($financeiro->tipo == 'avista' && ($financeiro->socorro_em_rota == 'nao') &&  $financeiro->pedido != null) {
                    $message->subject( 'PAGAMENTO A VISTA; PEDIDO: '. $financeiro->pedido . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $financeiro->placa );
                } elseif($financeiro->socorro_em_rota == 'sim'){
                    $message->subject( 'SOCORRO EM ROTA; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $financeiro->placa );
                } elseif($financeiro->adiantamento_fornecedor == 'sim'){
                    $message->subject( 'ADIANTAMENTO FORNECEDOR; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio );
                }
            
            if (!empty($financeiro->anexo_path) && Storage::disk('public')->exists($financeiro->anexo_path)) {
                $message->attach(storage_path('app/public/' . $financeiro->anexo_path));
            }
    }
        );
        }


// 2. Verifica se o switch do Pix Flow está ligado no banco
        $status_pix_fr = \Illuminate\Support\Facades\DB::table('configuracoes_sistema')
                ->where('chave', 'status_pix_fr')
                ->value('valor') ?? 0;

        if ($status_pix_fr == 1) {
            try {
                // Captura o array de retorno produzido pelo PixFlowService
                $retornoPix = $this->processarPix(
                    $financeiro->id,
                    $financeiro->tipo_pix, 
                    $financeiro->pix,  
                    $financeiro->valor,      
                    $financeiro->cnpj        
                );

                // Se a API aceitou e processou o pagamento
                if (isset($retornoPix['sucesso']) && $retornoPix['sucesso']) {
                    $sufixoIdempotente = ($retornoPix['idempotent'] ?? false) ? ' (Idempotência Ativa)' : '';
                    return "Solicitação aprovada e Pix enviado com sucesso! Status na FR: {$retornoPix['pix_status']}{$sufixoIdempotente}.";
                }

                // Se a API barrou por regras de negócio (DICT divergente, saldo insuficiente, etc.)
                return "Solicitação aprovada localmente, mas o PIX foi RECUSADO pela API. Motivo: {$retornoPix['mensagem']}";

            } catch (\Exception $e) {
                // Registra no arquivo de logs do Laravel para auditoria técnica
                Log::error("Falha técnica ao processar Pix automático para o ID: {$financeiro->id}. Erro: " . $e->getMessage());
                
                return "Solicitação aprovada localmente, mas ocorreu uma falha na comunicação com o servidor Pix Flow.";
            }
        } else {
            $financeiro->update(['status' => 'aprovado_gestor']);
        }

        // Se o switch estiver em OFF (0), segue o fluxo tradicional padrão
        return 'Solicitação aprovada com sucesso! (Módulo Pix Flow em OFF)';
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

        if ($financeiro->tipo == 'avista') {
            $gestor = $financeiro->unidadeAprovadora?->gestorRegional;

            if (!$gestor) {
                return 'Gestor regional não encontrado para esta unidade.';
            }

            $calculo = $gestor->saldo + $financeiro->valor;

            $gestor->saldo = $calculo;
            $gestor->save();
        }

        $financeiro->update(['status' => 'reprovado']);

        return 'Solicitação reprovada com sucesso! Seu saldo foi atualizado.';
    }




    public function finalizar_reembolso(Request $request, $id)
    {
        $financeiro = Financeiro::findOrFail($id);
        
        if ($financeiro->status !== 'aprovado') {
            return 'Solicitação já foi processada.';
        }

        $fileUrl = null;

        // 1. TRATAMENTO DO ARQUIVO
if ($request->hasFile('comprovante') && $request->file('comprovante')->isValid()) {
    $file = $request->file('comprovante');
    
    // 1. Gera o nome do arquivo
    $fileName = 'comprovante_' . $financeiro->id . '_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
    
    // 2. Define o caminho absoluto para a pasta (storage/app/public/comprovantes)
    $destinationPath = storage_path('app/public/comprovantes');

    // 3. Move o arquivo usando o método nativo (mais robusto contra o erro "Path cannot be empty")
    $file->move($destinationPath, $fileName);

    // 4. Salva o caminho RELATIVO no banco para o asset() e Storage:: buscar depois
    $pathParaBanco = 'comprovantes/' . $fileName;

    $financeiro->comprovante_pagamento = $pathParaBanco;
    $financeiro->status = 'finalizado'; // Já atualiza o status aqui
    $financeiro->save();

    $fileUrl = asset('storage/' . $pathParaBanco);
} else {
    return back()->withErrors(['comprovante' => 'Arquivo inválido ou não selecionado.']);
}

        // 2. VALIDAÇÃO DE USUÁRIO
        $user = Auth::user();
        if($user === null){
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
        }


            // CONSULTANDO SE TEM PERMISSÃO PARA ACESSAR O BI
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        if (!$user->temSetor(['admin'])){

        // 5. DISPARO DE E-MAILS
        if ($financeiro->tipo == 'reembolso') {
            Mail::send('emails.financeiro_reembolso_financeiro', [
                'financeiro' => $financeiro, 
                'link_comprovante' => $fileUrl, // Enviando o link para a View
                'user' => $user
            ], function($message) use ($financeiro, $emails){
                
                //$message->to('higor.machado@grupocargopolo.com.br');
                $message->to('contasapagar@grupocargopolo.com.br');
                $message->cc($emails);

                // Assuntos dinâmicos
                $message->subject( 'COMPROVANTE DISPONÍVEL - REEMBOLSO; PROTOCOLO:'. $financeiro->id  . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades?->unidade_negocio );

            
                // Anexo do Comprovante
                if (Storage::disk('public')->exists($financeiro->comprovante_pagamento)) {
                    $message->attach(storage_path('app/public/' . $financeiro->comprovante_pagamento), [
                        'as' => 'comprovante_pagamento.' . pathinfo($financeiro->comprovante_pagamento, PATHINFO_EXTENSION)
                    ]);
                }
            });
        }
        }
        

        // 7. ATUALIZAÇÃO FINAL
        $financeiro->update(['status' => 'finalizado']);

        return redirect()->route('financeiro.resumo')->with('success', 'Solicitação realizada com sucesso!');
        // return 'Solicitação aprovada com sucesso! Pagamento finalizado! Link enviado por e-mail. ';
    }




// APROVAR - gestor clicou no link -> dispara para os setores
    public function aprovar_financeiro(Request $request, $id)
    {
        $financeiro = Financeiro::findOrFail($id);
        
        if ($financeiro->status !== 'aprovado_gestor') {
            return 'Solicitação já foi processada.';
        }

        $fileUrl = null;

        // 1. TRATAMENTO DO ARQUIVO
if ($request->hasFile('comprovante') && $request->file('comprovante')->isValid()) {
    $file = $request->file('comprovante');
    
    // 1. Gera o nome do arquivo
    $fileName = 'comprovante_' . $financeiro->id . '_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
    
    // 2. Define o caminho absoluto para a pasta (storage/app/public/comprovantes)
    $destinationPath = storage_path('app/public/comprovantes');

    // 3. Move o arquivo usando o método nativo (mais robusto contra o erro "Path cannot be empty")
    $file->move($destinationPath, $fileName);

    // 4. Salva o caminho RELATIVO no banco para o asset() e Storage:: buscar depois
    $pathParaBanco = 'comprovantes/' . $fileName;

    $financeiro->comprovante_pagamento = $pathParaBanco;
    $financeiro->status = 'finalizado'; // Já atualiza o status aqui
    $financeiro->save();

    $fileUrl = asset('storage/' . $pathParaBanco);
} else {
    return back()->withErrors(['comprovante' => 'Arquivo inválido ou não selecionado.']);
}


        // 2. VALIDAÇÃO DE USUÁRIO
        $user = Auth::user();
        if($user === null){
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
        }

        // 3. TRATAMENTO DE E-MAILS
        $emailsString = $financeiro->emails;
        $emailsValidos = [];

        if (!empty($emailsString)) {
            $emailsArray = array_map('trim', explode(';', $emailsString));
            $emailsValidos = array_filter($emailsArray, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            if (count($emailsValidos) !== count($emailsArray)) {
                return back()->withErrors(['emails' => 'Um ou mais e-mails são inválidos.']);
            }
        }

        $emails = array_filter([
            ...$emailsValidos,
            $financeiro->email,
            $financeiro->email_gestor,
            $financeiro->gestor_aprovador,
            $user->email,
            $financeiro->unidadeAprovadora?->gestorRegional?->email_gestor
        ], function ($email) {
            return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        });

        $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

        // 4. LÓGICA DE INTEGRAÇÃO (RAZ / SQL)
        if($financeiro->tem_nota_fiscal == 'sim' && $financeiro->tipo == 'avista'){
            $financeiro->update(['id_raz' => DB::connection('sqlsrv')->table('BANRAZ')->max('ID_RAZ') + 1]);
        } else {
            $financeiro->update(['id_raz' => $financeiro->fornecedor. '-A-' . $financeiro->fornecedor .'-'. $financeiro->id]);
        }

            // CONSULTANDO SE TEM PERMISSÃO PARA ACESSAR O BI
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        if (!$user->temSetor(['admin'])){

        // 5. DISPARO DE E-MAILS
        if ($financeiro->tipo == 'avista' || $financeiro->tipo == 'ajuda_de_custo') {
            Mail::send('emails.financeiro_avista_financeiro', [
                'financeiro' => $financeiro, 
                'link_comprovante' => $fileUrl, // Enviando o link para a View
                'user' => $user
            ], function($message) use ($financeiro, $emails){
                
                //$message->to('higor.machado@grupocargopolo.com.br');
                $message->to('contasapagar@grupocargopolo.com.br');
                $message->cc($emails);

                // Assuntos dinâmicos
                if ($financeiro->tipo == 'avista' && ($financeiro->socorro_em_rota == 'nao') && ( $financeiro->pedido == '000000' || $financeiro->pedido == null )){
                    $message->subject( 'COMPROVANTE DISPONÍVEL - PAGAMENTO A VISTA; PROTOCOLO:'. $financeiro->id  . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio );
                } elseif ($financeiro->tipo == 'avista' && ($financeiro->socorro_em_rota == 'nao') &&  $financeiro->pedido != null) {
                    $message->subject( 'COMPROVANTE DISPONÍVEL - PAGAMENTO A VISTA; PEDIDO: '. $financeiro->pedido . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio );
                } elseif($financeiro->socorro_em_rota == 'sim'){
                    $message->subject( 'COMPROVANTE DISPONÍVEL - SOCORRO EM ROTA; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio . ' PLACA: ' . $financeiro->placa );
                } elseif($financeiro->adiantamento_fornecedor == 'sim'){
                    $message->subject( 'COMPROVANTE DISPONÍVEL - ADIANTAMENTO FORNECEDOR; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio );
                } elseif($financeiro->tipo == 'ajuda_de_custo'){
                    $message->subject( 'COMPROVANTE DISPONÍVEL - AJUDA DE CUSTO; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $financeiro->name . ' FILIAL: ' . $financeiro->unidades->unidade_negocio );
                }
            
                // Anexo do Comprovante
                if (Storage::disk('public')->exists($financeiro->comprovante_pagamento)) {
                    $message->attach(storage_path('app/public/' . $financeiro->comprovante_pagamento), [
                        'as' => 'comprovante_pagamento.' . pathinfo($financeiro->comprovante_pagamento, PATHINFO_EXTENSION)
                    ]);
                }
            });
        }
        }

        // 6. EXECUÇÃO DE PROCEDURES FINANCEIRAS
        if($financeiro->tem_nota_fiscal == 'sim' && $financeiro->tipo == 'avista'){
            $financeiro->financeiroAvista($financeiro->id, $financeiro->valor, $financeiro->solicitante, $financeiro->fornecedor, $financeiro->pedido,$financeiro->placa,$financeiro->unidadeAprovadora?->nome_gestor, $financeiro->cod_unidade, $financeiro->unidades->conta);
        // } elseif ($financeiro->tipo == 'ajuda_de_custo') {
        //     $financeiro->pagdoc($financeiro->fornecedor, $financeiro->valor, $financeiro->id, $financeiro->cod_unidade, $financeiro->cod_custo, $financeiro->cod_gasto,'LETICIA CARVALHO', 60, 52);
        } else{
            $financeiro->pagdoc($financeiro->fornecedor, $financeiro->valor, $financeiro->id, $financeiro->cod_unidade, $financeiro->cod_custo, $financeiro->cod_gasto,$financeiro->unidadeAprovadora?->nome_gestor, 376 , 83 );
        }

        // 7. ATUALIZAÇÃO FINAL
        $financeiro->update(['status' => 'finalizado']);

        return redirect()->route('financeiro.resumo')->with('success', 'Solicitação realizada com sucesso!');
        // return 'Solicitação aprovada com sucesso! Pagamento finalizado! Link enviado por e-mail. ';
    }


    
    
    public function reprovar_financeiro(Request $request, $id)
    {
        $financeiro = Financeiro::findOrFail($id);

        if ($financeiro->status !== 'aprovado_gestor') {
            return 'Solicitação já foi processada.';
        }

        // Obtém o usuário autenticado
        $user = Auth::user();

        if($user === null){
            //rota login
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
        }

        $request->validate([
                'motivo_reprovacao' => 'required|string|min:5'
        ]);

        $financeiro->motivo_reprovacao = $request->motivo_reprovacao;
        $financeiro->save();




        $financeiro->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

        Mail::send('emails.financeiro_reprovado_financeiro', ['financeiro' => $financeiro,'user' => $user], function($message) use ($financeiro,$user){
            $message->to([$financeiro->email,$user->email]);
            $message->cc([$financeiro->email_gestor,$financeiro->gestor_aprovador]);
            $message->subject('Solicitação Reprovada - Protocolo: ' . $financeiro->id);
        });

        if ($financeiro->tipo == 'avista') {

            $gestor = $financeiro->unidadeAprovadora?->gestorRegional;

            if (!$gestor) {
                return 'Gestor regional não encontrado para esta unidade.';
            }

            $calculo = $gestor->saldo + $financeiro->valor;

            $gestor->saldo = $calculo;
            $gestor->save();
        }

        $financeiro->update(['status' => 'reprovado']);

        return redirect()->route('financeiro.resumo')->with('success', 'Solicitação realizada com sucesso!');
    }


    /**
     * Display the specified resource.
     */
public function saldo()
{
    $user = auth()?->user();
    if (!$user) return redirect()->route('login.form');

    if ($user->temSetor(['financeiro', 'admin', 'diretoria'])) {

    $dataInicioMes = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');

    $saldo = GestorFinanceiro::query()
        ->addSelect(['total_gasto' => function ($query) use ($dataInicioMes) {
            $query->selectRaw('COALESCE(SUM(valor), 0)')
                ->from('financeiros')
                ->whereIn('status', ['aprovado_gestor', 'finalizado', 'aprovado', 'pago'])
                ->where('tipo', 'avista')
                ->where('created_at', '>=', $dataInicioMes)
                ->whereIn('gestor_aprovador', function ($sub) {
                    $sub->select('email_gestor')
                        ->from('unidades_negocio')
                        // Faz a ligação: O regional da unidade deve ser o gestor desta linha
                        ->whereRaw('unidades_negocio.email_regional COLLATE utf8mb4_unicode_ci = gestores_financeiro.email_gestor COLLATE utf8mb4_unicode_ci');
                });
        }])
        ->with(['unidadeNegocio'])
        ->get();

        $unidades = UnidadesNegocio::orderBy('unidade_negocio')->get();

        return view('financeiro_fr.saldos', compact('saldo', 'unidades'));
    }
    
    abort(403);
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

    public function update_novo_saldo(Request $request)
    {
        $saldo = GestorFinanceiro::find($request->id);

        if (!$saldo) {
            return response()->json(['success' => false, 'message' => 'Registro não encontrado']);
        }

        $saldo->novo_saldo = $request->saldo;
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


public function buscarDadosPedido($numped)
{
    try {
        // Busca os dados do rateio no Rodopar
        $dados = DB::connection('sqlsrv')
            ->table('PEDRAT')
            ->where('NUMPED', $numped)
            ->select('CODUNN', 'CODCUS', 'CODCGA')
            ->first();

        if (!$dados) {
            return response()->json(['erro' => 'Pedido não encontrado'], 404);
        }

        return response()->json($dados);

    } catch (\Exception $e) {
        // Se der erro, ele retorna a mensagem real para você ver no console do navegador
        return response()->json(['erro' => $e->getMessage()], 500);
    }
}


public function consultarPedido(Request $request, $valor)
{
    // Verifica se o usuário escolheu ID, caso contrário usa PEDIDO por padrão
    $campo = $request->query('tipo') === 'id' ? 'id' : 'pedido';

    // Faz a busca dinâmica baseada na escolha
    $registro = Financeiro::where($campo, $valor)->first();

    if ($registro) {
        return response()->json([
            'sucesso' => true,
            'id' => $registro->id,
            'favorecido' => $registro->favorecido,
            'status' => $registro->status,
            'valor' => number_format((float)$registro->valor, 2, ',', '.'),
            'tipo' => $registro->tipo,
            'socorro_em_rota' => $registro->socorro_em_rota,
            'frota_bloqueada' => $registro->frota_bloqueada,
            'gestor_aprovador' => $registro->gestor_aprovador
        ]);
    }

    return response()->json(['sucesso' => false]);
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



    public function atualizarStatusPix(Request $request)
{
    $user = auth()?->user();

    if (!$user) {
        return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
    }

    //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
    if ($user->temSetor(['admin', 'diretoria'])) {
        // Permissão concedida
    } else {
        return response()->json(['sucesso' => false, 'message' => 'Acesso negado.'], 403);
    }



    // Valida se o valor recebido é estritamente 0 ou 1
    $request->validate([
        'status_pix_fr' => 'required|in:0,1'
    ]);

    try {
        $novoStatus = $request->status_pix_fr;

        // Opção A: Se você usa uma tabela de parâmetros/configurações globais do sistema:
        DB::table('configuracoes_sistema') // Substitua pelo nome real da sua tabela
            ->where('chave', 'status_pix_fr') // Ajuste o filtro conforme sua estrutura
            ->update(['valor' => $novoStatus]);

        // Retorna a resposta positiva para o JavaScript redesenhar a tela
        return response()->json(['sucesso' => true]);

    } catch (\Exception $e) {
        return response()->json([
            'sucesso' => false, 
            'erro' => $e->getMessage()
        ], 500);
    }
}





protected function processarPix($id, $tipoPixBanco, $chavePix, $valor, $cnpj)
{
    $pixFlowService = app(\App\Services\PixFlowService::class);

    // Limpa pontuações do documento para enviar puro para o DICT
    $documentoTratado = preg_replace('/[^0-9]/', '', $cnpj); 
    $chaveTratada = trim($chavePix);

    // --- CONVERSOR DE-PARA DE TIPOS DE CHAVE ---
    $tipoChaveAPI = 'aleatoria'; // Valor padrão caso não encontre correspondência

    // Padroniza a string removendo espaços e jogando para minúsculo
    $tipoPixFormatado = mb_strtolower(trim($tipoPixBanco));

    switch ($tipoPixFormatado) {
        case 'cpf/cnpj':
            // Limpa a chave pix para contar os dígitos e descobrir se é CPF ou CNPJ
            $chaveNumerica = preg_replace('/[^0-9]/', '', $chaveTratada);
            $tipoChaveAPI = (strlen($chaveNumerica) > 11) ? 'cnpj' : 'cpf';
            break;

        case 'e-mail':
        case 'email':
            $tipoChaveAPI = 'email';
            break;

        case 'celular':
        case 'telefone':
            $tipoChaveAPI = 'telefone';
            break;

        case 'chave aleatória':
        case 'chave aleatoria':
        case 'aleatoria':
            $tipoChaveAPI = 'aleatoria';
            break;
    }
    // -------------------------------------------

    // Monta o payload definitivo com o tipo traduzido
    $payload = [
        "external_id"            => "pedido-" . $id,
        "valor_centavos"         => (int) round($valor * 100),
        "chave_pix"              => $chaveTratada,
        "tipo_chave"             => $tipoChaveAPI, // Envia 'cpf', 'cnpj', 'email', 'telefone' ou 'aleatoria'
        "beneficiario_documento" => $documentoTratado
    ];

    // Dispara a integração contra o Service
    $retorno = $pixFlowService->criarSolicitacao($payload);

    if ($retorno['sucesso']) {
        \Illuminate\Support\Facades\DB::table('financeiros')
            ->where('id', $id)
            ->update([
                'pix_flow_id' => $retorno['id'],
                'status'      => 'processando' // Atualiza o status para acompanhar o banco da FR
            ]);
    } else {
        \Illuminate\Support\Facades\DB::table('financeiros')
            ->where('id', $id)
            ->update([
                'status' => 'falha_integracao_pix',
                'motivo_rejeicao_interno' => $retorno['mensagem']
            ]);
    }

    return $retorno;
}




}

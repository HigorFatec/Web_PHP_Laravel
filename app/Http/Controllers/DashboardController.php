<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;

use App\Models\Reserva;
use App\Models\Veiculo;
use App\Models\Hospedagem;
use App\Models\Adiantamento;

use App\Models\User;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache; // Não esqueça do import


class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate')) : null;
        $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate')) : null;


        $usuariosQuery = User::query();
        if ($startDate && $endDate) {
            $usuariosQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $usuarios = $usuariosQuery->count();

        // Dados do gráfico 1
        $usersData = $usuariosQuery->select([
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        ])
        ->groupBy('month')
        ->orderBy('month','asc')
        ->get();

        $month = [];
        $total = [];

        foreach ($usersData as $user) {
            $month[] = $user->month;
            $total[] = $user->total;
        }

        $userLabel = "'Comparativo de cadastros de usuários por mês'";
        $usermonth = implode(',', $month);
        $userTotal = implode(',', $total);



        // Capturar as datas do Request
        $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate')) : null;
        $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate')) : null;

        // Contagem total de cada tipo
        $passagensQuery = Reserva::query();
        $veiculosQuery = Veiculo::query();
        $hospedagensQuery = Hospedagem::query();
        $adiantamentosQuery = Adiantamento::query();

        // Aplicar o filtro de data, se disponível
        if ($startDate && $endDate) {
            $passagensQuery->whereBetween('created_at', [$startDate, $endDate]);
            $veiculosQuery->whereBetween('created_at', [$startDate, $endDate]);
            $hospedagensQuery->whereBetween('created_at', [$startDate, $endDate]);
            $adiantamentosQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $passagens = $passagensQuery->count();
        $veiculos = $veiculosQuery->count();
        $hospedagens = $hospedagensQuery->count();
        $adiantamentos = $adiantamentosQuery->count();

        // Contagem total de cancelados para cada tipo
        $passagensCanceladasQuery = Reserva::where('status', 'cancelada');
        $veiculosCanceladosQuery = Veiculo::where('status', 'cancelada');
        $hospedagensCanceladasQuery = Hospedagem::where('status', 'cancelada');
        $adiantamentosCanceladosQuery = Adiantamento::where('status', 'cancelada');

        // Aplicar o filtro de data nos cancelados, se disponível
        if ($startDate && $endDate) {
            $passagensCanceladasQuery->whereBetween('updated_at', [$startDate, $endDate]);
            $veiculosCanceladosQuery->whereBetween('updated_at', [$startDate, $endDate]);
            $hospedagensCanceladasQuery->whereBetween('updated_at', [$startDate, $endDate]);
            $adiantamentosCanceladosQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $passagensCanceladas = $passagensCanceladasQuery->count();
        $veiculosCancelados = $veiculosCanceladosQuery->count();
        $hospedagensCanceladas = $hospedagensCanceladasQuery->count();
        $adiantamentosCancelados = $adiantamentosCanceladosQuery->count();

        // Total geral
        $total = $passagens + $veiculos + $hospedagens + $adiantamentos;
        $totalCancelados = $passagensCanceladas + $veiculosCancelados + $hospedagensCanceladas + $adiantamentosCancelados;






        // Cálculo das reservas no prazo e fora do prazo
        //$reservas = Reserva::with('user')->get(); // Inclua o relacionamento
        //$prazoveiculo = Veiculo::all();
        //$prazohospedagem = Hospedagem::all();


        // Aplicando o filtro de data
        $reservas = $passagensQuery->with('user')->get();
        $prazoveiculo = $veiculosQuery->with('user')->get();
        $prazohospedagem = $hospedagensQuery->with('user')->get();
        $prazoAdiantamento = $adiantamentosQuery->with('user')->get();


        $prazo = 0;
        $fora_do_prazo = 0;


        $foraDoPrazoReservas = [];

        foreach ($reservas as $reserva) {
            $dias_antecedencia = Carbon::parse($reserva->ida)->startOfDay()->diffInDays(Carbon::parse($reserva->created_at)->startOfDay());

            // Converter datas para instâncias de Carbon
            $dataIda = Carbon::parse($reserva->ida);
            $dataCriacao = Carbon::parse($reserva->created_at);

            if (($reserva->tipo == 'aerea' && $dias_antecedencia >= 10) ||
                ($reserva->tipo == 'rodoviaria' && $dias_antecedencia >= 5)) {
                $prazo++;
            } else {
                $fora_do_prazo++;
                $foraDoPrazoReservas[] = [
                    'id' => $reserva->id,
                    'user_name' => $reserva->user->name,
                    'filial' => $reserva->user->filial,
                    'origem' => $reserva->origem,
                    'destino' => $reserva->destino,
                    'ida' => $reserva->ida,
                    'created_at' => $reserva->created_at,
                    'viagem' => $reserva->tipo,
                    'nome' => $reserva->nome,
                    'tipo' => 'Passagem', // Adiciona o tipo
                ]; // Adiciona a reserva fora do prazo ao array

        }
    }
        foreach($prazoveiculo as $veic){
            $dias_antecedencia = Carbon::parse($veic->ida)->startOfDay()->diffInDays(Carbon::parse($veic->created_at)->startOfDay());

            if($dias_antecedencia >= 5){
                $prazo++;
            } else {
                $fora_do_prazo++;
                $foraDoPrazoReservas[] = [
                    'id' => $veic->id,
                    'user_name' => $veic->user->name,
                    'filial' => $veic->user->filial,
                    'origem' => $veic->origem,
                    'destino' => $veic->destino,
                    'ida' => $veic->ida,
                    'created_at' => $veic->created_at,
                    'viagem' => '',
                    'nome' => $veic->nome,
                    'tipo' => 'Reserva de Veiculo Leve', // Adiciona o tipo
                ]; // Adiciona a reserva fora do prazo ao array


            }
        }

        //adiantamento
        foreach($prazohospedagem as $hosp){
            $dias_antecedencia = Carbon::parse($hosp->ida)->startOfDay()->diffInDays(Carbon::parse($hosp->created_at)->startOfDay());

            if($dias_antecedencia >= 5){
                $prazo++;
            } else {
                $fora_do_prazo++;
                $foraDoPrazoReservas[] = [
                    'id' => $hosp->id,
                    'user_name' => $hosp->user->name,
                    'filial' => $hosp->user->filial,
                    'origem' => $hosp->origem,
                    'destino' => $hosp->destino,
                    'ida' => $hosp->ida,
                    'created_at' => $hosp->created_at,
                    'viagem' => '',
                    'nome' => $hosp->nome,
                    'tipo' => 'Hospedagem', // Adiciona o tipo
                ]; // Adiciona a reserva fora do prazo ao array

            }
        }

        foreach($prazoAdiantamento as $adiantamento){
            
            $volta_x_ida = Carbon::parse($adiantamento->ida)->startOfDay()->diffInDays(Carbon::parse($adiantamento->volta)->startOfDay());

            if($volta_x_ida >= 5){
                $prazo++;
            } else {
                $fora_do_prazo++;
                $foraDoPrazoReservas[] = [
                    'id' => $adiantamento->id,
                    'user_name' => $adiantamento->user->name,
                    'filial' => $adiantamento->user->filial,
                    'origem' => $adiantamento->origem,
                    'destino' => $adiantamento->destino,
                    'ida' => $adiantamento->ida,
                    'created_at' => $adiantamento->created_at,
                    'viagem' => '',
                    'nome' => $adiantamento->nome,
                    'tipo' => 'Adiantamento', // Adiciona o tipo
                ]; // Adiciona a reserva fora do prazo ao array

            }
        }


        $foraDoPrazoPorFilial = [];
        $foraDoPrazoPorColaborador = [];
        
        $NoPrazoPorFilial = [];
        $NoPrazoPorColaborador = [];


        // Função para processar reservas e outros itens
        function processarReservas($reservas, &$foraDoPrazoPorFilial, &$foraDoPrazoPorColaborador, &$NoPrazoPorFilial, &$NoPrazoPorColaborador) {
            foreach ($reservas as $reserva) {
                $dias_antecedencia = Carbon::parse($reserva->ida)->diffInDays($reserva->created_at);

                if (($reserva->tipo == 'aerea' && $dias_antecedencia >= 10) ||
                    ($reserva->tipo == 'rodoviaria' && $dias_antecedencia >= 5)||
                    ($reserva->tipo == null && $dias_antecedencia >= 5)) {
                    
                    if(!isset($NoPrazoPorFilial[$reserva->user->filial])) {
                        $NoPrazoPorFilial[$reserva->user->filial] = 1;
                    } else {
                        $NoPrazoPorFilial[$reserva->user->filial]++;
                    }
                    if(!isset($NoPrazoPorColaborador[$reserva->user->name])) {
                        $NoPrazoPorColaborador[$reserva->user->name] = 1;
                    } else {
                        $NoPrazoPorColaborador[$reserva->user->name]++;
                    }

                    continue;
                }

                if (!isset($foraDoPrazoPorFilial[$reserva->user->filial])) {
                    $foraDoPrazoPorFilial[$reserva->user->filial] = 1;
                } else {
                    $foraDoPrazoPorFilial[$reserva->user->filial]++;
                }

                if(!isset($foraDoPrazoPorColaborador[$reserva->user->name])) {
                    $foraDoPrazoPorColaborador[$reserva->user->name] = 1;
                } else {
                    $foraDoPrazoPorColaborador[$reserva->user->name]++;
                }
            }
        }
        // Processar reservas
        processarReservas($reservas, $foraDoPrazoPorFilial, $foraDoPrazoPorColaborador, $NoPrazoPorFilial, $NoPrazoPorColaborador);
        processarReservas($prazoveiculo, $foraDoPrazoPorFilial, $foraDoPrazoPorColaborador ,$NoPrazoPorFilial, $NoPrazoPorColaborador);
        processarReservas($prazohospedagem, $foraDoPrazoPorFilial, $foraDoPrazoPorColaborador ,$NoPrazoPorFilial, $NoPrazoPorColaborador);
        processarReservas($prazoAdiantamento, $foraDoPrazoPorFilial, $foraDoPrazoPorColaborador ,$NoPrazoPorFilial, $NoPrazoPorColaborador);

        // Preparar dados para gráficos
        $filiais = array_keys($foraDoPrazoPorFilial);
        $colaboradores = array_keys($foraDoPrazoPorColaborador);
        $noPrazoFiliais = array_keys($NoPrazoPorFilial);
        $noPrazoColaboradores = array_keys($NoPrazoPorColaborador);

        // Ordernar dados
        arsort($foraDoPrazoPorFilial);
        arsort($foraDoPrazoPorColaborador);
        arsort($NoPrazoPorFilial);
        arsort($NoPrazoPorColaborador);

        // Truncando os nomes para os primeiros 10 caracteres
        $colaboradores = array_map(function($colaborador) {
            return substr($colaborador, 0, 15);
        }, $colaboradores);
        $noPrazoColaboradores = array_map(function($noPrazoColaboradores) {
            return substr($noPrazoColaboradores, 0, 15);
        }, $noPrazoColaboradores);

        $foraDoPrazoPorFilialData = array_values($foraDoPrazoPorFilial);
        $foraDoPrazoPorColaboradorData = array_values($foraDoPrazoPorColaborador);
        $NoPrazoPorFilialData = array_values($NoPrazoPorFilial);
        $NoPrazoPorColaboradorData = array_values($NoPrazoPorColaborador);


        return view('admin.dashboard', compact(
            'usuarios',
            'userLabel',
            'usermonth',
            'userTotal',
            'total',
            'totalCancelados',
            'passagens',
            'veiculos',
            'hospedagens',
            'adiantamentos',
            'prazo',
            'passagensCanceladas',
            'veiculosCancelados',
            'hospedagensCanceladas',
            'adiantamentosCancelados',
            'fora_do_prazo',
            'foraDoPrazoReservas',
            // Adicione as variáveis para as reservas fora do prazo
            'foraDoPrazoPorFilial',
            'foraDoPrazoPorColaborador',
            'filiais',
            'colaboradores',
            'foraDoPrazoPorFilialData',
            'foraDoPrazoPorColaboradorData',
            'NoPrazoPorFilialData',
            'NoPrazoPorColaboradorData',
            'noPrazoColaboradores',
            'startDate',
            'endDate'


        ));
    }


    public function exibirBI()
{
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
        $reportId = env('POWERBI_REPORT_ID_RESERVA');

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






    public function exibirBI_PNEU()
{
    $user = auth()->user();

    if (!$user || !$user->temSetor(['frotas', 'admin'])) {
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
        $reportId = env('POWERBI_REPORT_ID_PNEU');

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

        return view('financeiro_fr.pneu_bi', compact('embedToken', 'embedUrl', 'reportId', 'veioDoCache'));

    } catch (\Exception $e) {
        return "Erro no BI: " . $e->getMessage();
    }
}



public function exibirBI_MANUTENCAO()
{
    $user = auth()->user();

    if (!$user || !$user->temSetor(['frotas', 'admin'])) {
        return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
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
        $reportId = env('POWERBI_REPORT_ID_MANUTENCAO');

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

        return view('manutencao.manutencao_bi', compact('embedToken', 'embedUrl', 'reportId', 'veioDoCache'));

    } catch (\Exception $e) {
        return "Erro no BI: " . $e->getMessage();
    }
}



public function exibirBI_florestal()
{
    $user = auth()->user();

    if (!$user || !$user->temSetor(['frotas', 'admin'])) {
        return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
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
        $reportId = env('POWERBI_REPORT_ID_DIRETORIA_JENIVAL');

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

        return view('powerbi.florestal', compact('embedToken', 'embedUrl', 'reportId', 'veioDoCache'));

    } catch (\Exception $e) {
        return "Erro no BI: " . $e->getMessage();
    }
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dashboard $dashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dashboard $dashboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dashboard $dashboard)
    {
        //
    }
}

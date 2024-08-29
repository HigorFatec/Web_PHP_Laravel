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


        $prazo = 0;
        $fora_do_prazo = 0;


        $foraDoPrazoReservas = [];

        foreach ($reservas as $reserva) {
            $dias_antecedencia = Carbon::parse($reserva->ida)->diffInDays($reserva->created_at);

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
            $dias_antecedencia = Carbon::parse($veic->ida)->diffInDays($veic->created_at);

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

        foreach($prazohospedagem as $hosp){
            $dias_antecedencia = Carbon::parse($hosp->ida)->diffInDays($hosp->created_at);

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


        $foraDoPrazoPorFilial = [];
        $foraDoPrazoPorColaborador = [];


        // Função para processar reservas e outros itens
        function processarReservas($reservas, &$foraDoPrazoPorFilial, &$foraDoPrazoPorColaborador) {
            foreach ($reservas as $reserva) {
                $dias_antecedencia = Carbon::parse($reserva->ida)->diffInDays($reserva->created_at);

                if (($reserva->tipo == 'aerea' && $dias_antecedencia >= 10) ||
                    ($reserva->tipo == 'rodoviaria' && $dias_antecedencia >= 5)||
                    ($reserva->tipo == null && $dias_antecedencia >= 5)) {
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
        processarReservas($reservas, $foraDoPrazoPorFilial, $foraDoPrazoPorColaborador);
        processarReservas($prazoveiculo, $foraDoPrazoPorFilial, $foraDoPrazoPorColaborador);
        processarReservas($prazohospedagem, $foraDoPrazoPorFilial, $foraDoPrazoPorColaborador);

        // Preparar dados para gráficos
        $filiais = array_keys($foraDoPrazoPorFilial);
        $colaboradores = array_keys($foraDoPrazoPorColaborador);

        $foraDoPrazoPorFilialData = array_values($foraDoPrazoPorFilial);
        $foraDoPrazoPorColaboradorData = array_values($foraDoPrazoPorColaborador);


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
            'startDate',
            'endDate'


        ));
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

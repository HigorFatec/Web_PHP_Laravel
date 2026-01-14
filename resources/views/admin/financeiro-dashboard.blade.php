@extends('layout')
@section('title', 'Dashboard Financeiro')
@section('conteudo')

<ul id="dropdown2" class="dropdown-content">
    <li><a href="{{route('reserva.reservas')}}">Minhas reservas</a></li>
    @auth
        @if (auth()->user()->admin == 1)
        <li><a href="{{route('admin.dashboard')}}">Dashboard Reservas</a></li>
        {{-- Adicione a rota para o Dashboard Financeiro aqui --}}
        <li><a href="{{route('admin.financeiro-dashboard')}}">Dashboard Financeiro</a></li> 
        <li><a href="{{route('admin.canceladas')}}">Canceladas</a></li>
        <li><a href="{{route('admin.finalizadas')}}">Finalizadas</a></li>
        @endif 
    @endauth
    <li><a href="{{route('login.logout')}}">Sair</a></li>
</ul>

  <nav class="blue darken-4">
    <div class="nav-wrapper container ">
        <a href="#" class="brand-logo center">Dashboard Financeiro</a>
        <a href="#" class="brand-logo" href="index.html">
        <img src="{{ asset('img/LogoSite.png') }}" style="width: 100px; height: auto; margin:10px;margin-left:80px">
        </a>
        <ul id="nav-mobile" class="brand-logo center">
            <li class="hide-on-med-and-down">
                <i class="material-icons left" style="margin-left:400px">account_balance</i>
            </li>
        </ul>
        <ul class="right ">                                 
            <li class="hide-on-med-and-down"><a href="#" onclick="fullScreen()"><i class="material-icons">settings_overscan</i> </a> </li>
            <li><a href="#" class="dropdown-trigger" data-target='dropdown2'> Olá {{auth()->user()->name}}  <i class="material-icons right">expand_more</i> </a></li>     
        </ul>
        <a href="#" data-target="slide-out-financeiro" class="sidenav-trigger left show-on-large"><i class="material-icons">menu</i></a>
    </div>
</nav>

<ul id="slide-out-financeiro" class="sidenav " >
    <li><div class="user-view">
        <div class="background deep-blue darken-4">
           <img src="{{asset('img/office2.jpg')}}" style="opacity: 0.5"> 
        </div>
        <a href="#user"><img class="circle" src="https://upload.wikimedia.org/wikipedia/commons/a/a6/Anonymous_emblem.svg"></a>
        <a href="#name"><span class="white-text name"> {{auth()->user()->name }} </span></a>
        <a href="#email"><span class="white-text email"> {{auth()->user()->email}} </span></a>
    </div></li> 

    <li><a href="{{route('reserva.home')}}"><i class="material-icons">home</i>Home</a></li>

    <div class="user-view">
        <form id="financeiro-filter-form">
            <label for="date-filter">Filtrar por Data de Criação:</label>
            <input type="date" id="date-filter-start" name="startDate" placeholder="Data Início" value="{{ $startDate ? $startDate->format('Y-m-d') : ''}}">
            <input type="date" id="date-filter-end" name="endDate" placeholder="Data Fim" value="{{ $endDate ? $endDate->format('Y-m-d') : ''}}"><br><br>

            {{-- NOVO CAMPO DE FILTRO POR TIPO --}}
            <label for="tipo-filter">Filtrar por Tipo:</label>
            <select id="tipo-filter" name="tipo">
                <option value="">Todos os Tipos</option>
                @foreach($tiposLabels as $tipo)
                    {{-- Verifica se o tipo atual foi o tipo selecionado na requisição --}}
                    <option value="{{ $tipo }}" {{ ($selectedTipo ?? '') == $tipo ? 'selected' : '' }}>
                        {{ $tipo }}
                    </option>
                @endforeach
            </select>
            <br>
            {{-- FIM NOVO CAMPO --}}

            <div class="container-filtrar">
                <button type="button" id="apply-financeiro-date-filter" class="btn-filtrar deep-purple">Aplicar Filtro</button> 
                <button type="button" id="clear-financeiro-filters" class="btn-filtrar deep-purple">Limpar Filtros</button>
            </div>
        </form>
    </div>
</ul>

@auth
@if (auth()->user()->admin == 1 || auth()->user()->admin == 1)

<div class="row container">

    <section class="info">
        <div class="col s12 m4">
            <article class="bg-gradient-green card z-depth-4">
                <i class="material-icons">attach_money</i>
                <p>Valor Total</p>
                {{-- <h3>R$ {{ number_format($valorTotal, 2, ',', '.') }}</h3>        --}}
                <h3> {{ $valorTotalFormatado }}</h3>            
            </article>
        </div>

        <div class="col s12 m4">
            <article class="bg-gradient-blue card z-depth-4">
                <i class="material-icons">receipt</i>
                <p>Total de Pedidos</p>
                <h3>{{$totalPedidos}} </h3>           
            </article>
        </div>


        <div class="col s12 m4">
            <article class="bg-gradient-orange card z-depth-4 ">
                <i class="material-icons">trending_up</i>
                <p>Valor Médio</p>
                {{-- <h3>R$ {{ number_format($valorMedio, 2, ',', '.') }}</h3>             --}}
                <h3> {{ $valorMedioFormatado }}</h3>            
            </article>
        </div>
    </section>
</div>

<div class="row container ">
    <section class="graficos col s12 m6" >            
        <div class="grafico card z-depth-4">
            <h5 class="center"> Movimentação Financeira Mensal</h5>
            <canvas id="movimentacaoMensalChart" width="400" height="200"></canvas>
        </div>           
    </section> 
    
    <section class="graficos col s12 m6">            
        <div class="grafico card z-depth-4">
            <h5 class="center"> Distribuição de Pedidos por Tipo </h5>
            <canvas id="distribuicaoTipoChart" width="400" height="200"></canvas> 
        </div>            
    </section>

    <section>
        <div class="graficos col s12 m6"> 
            <div class="grafico card z-depth-4">
                <h5 class="center"> Top 5 Filiais por Valor Transacionado</h5>
                <canvas id="topFiliaisChart" width="400" height="200"></canvas>
            </div> 
        </div> 
    </section>

    <section>
     <div class="graficos col s12 m6"> 
         <div class="grafico card z-depth-4">
                <h5 class="center"> Distribuição de Pedidos por Ano</h5>
                <canvas id="pedidosAnuaisChart" width="400" height="200"></canvas> 
          </div>
         </div>           
    </section>

    <section>
     <div class="graficos col s12 m6"> 
         <div class="grafico card z-depth-4">
              <h5 class="center"> Tendência do Valor Médio Mensal</h5>
             <canvas id="valorMedioMensalChart" width="400" height="200"></canvas>
          </div>
         </div>            
    </section>
</div>
{{-- FIM DOS GRÁFICOS --}}

@endif
@endauth

@endsection

@push('graficos')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // NOVO CÓDIGO JavaScript (apenas a função de formatação)
    function formatarMoeda(valor) {
        // Se o valor já é uma string "X.XX" (vindo do PHP):
        const valorString = String(valor);
        
        // Separa a parte inteira (antes do ponto) da decimal
        const partes = valorString.split('.');
        const parteInteira = partes[0];
        const parteDecimal = partes.length > 1 ? partes[1] : '00';

        // Adiciona separador de milhar (ponto)
        // O JavaScript não tem um bom formatador de moeda nativo para este formato
        const formatado = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        // Junta tudo com a vírgula e o prefixo R$
        return `R$ ${formatado},${parteDecimal}`;
    }
    
    // --- Configurações de Filtro de Data e Tipo ---
    document.getElementById('apply-financeiro-date-filter').addEventListener('click', function() {
        var startDate = document.getElementById('date-filter-start').value;
        var endDate = document.getElementById('date-filter-end').value;
        // Captura o valor do filtro de Tipo
        var tipo = document.getElementById('tipo-filter').value; 
        
        // Constrói a URL com todos os filtros
        let url = `{{ route('admin.financeiro-dashboard') }}?startDate=${startDate}&endDate=${endDate}`;
        
        // Adiciona o filtro de tipo SE ele for selecionado
        if (tipo) {
            url += `&tipo=${tipo}`;
        }
        
        window.location.href = url;
    });

    document.getElementById('clear-financeiro-filters').addEventListener('click', function() {
        document.getElementById('date-filter-start').value = '';
        document.getElementById('date-filter-end').value = '';
        document.getElementById('tipo-filter').value = ''; // Limpa o filtro de tipo
        
        // Redireciona para a rota limpa para remover os filtros
        window.location.href = `{{ route('admin.financeiro-dashboard') }}`;
    });

// --- GRÁFICOS CHART.JS ---

    // 1. Movimentação Mensal (Linha) - AGORA COM FORMATAÇÃO
    var ctx1 = document.getElementById('movimentacaoMensalChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: @json($meses),
            datasets: [{
                label: 'Valor (R$)',
                data: @json($totaisMensais),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            scales: { 
                y: { 
                    beginAtZero: true,
                    // CALLBACK PARA FORMATAR O EIXO Y
                    ticks: {
                        callback: function(value, index, ticks) {
                            return formatarMoeda(value);
                        }
                    }
                } 
            },
            plugins: {
                // CALLBACK PARA FORMATAR O TOOLTIP
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += formatarMoeda(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // 2. Distribuição por Tipo (Donut/Pie) - Não é monetário, MANTIDO
    var ctx2 = document.getElementById('distribuicaoTipoChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: @json($tiposLabels),
            datasets: [{
                label: 'Contagem de Pedidos',
                data: @json($tiposContagem),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                hoverOffset: 4
            }]
        }
    });

    // 3. Top Filiais por Valor (Barra) - AGORA COM FORMATAÇÃO
    var ctx3 = document.getElementById('topFiliaisChart').getContext('2d');
    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: @json($filiaisLabels),
            datasets: [{
                label: 'Valor Total (R$)',
                data: @json($filiaisTotais),
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { 
                y: { 
                    beginAtZero: true,
                    // CALLBACK PARA FORMATAR O EIXO Y
                    ticks: {
                        callback: function(value, index, ticks) {
                            return formatarMoeda(value);
                        }
                    } 
                } 
            },
            plugins: {
                // CALLBACK PARA FORMATAR O TOOLTIP
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += formatarMoeda(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
    
    // 4. Distribuição de Pedidos por Ano (Não é monetário, MANTIDO)
    var ctx4 = document.getElementById('pedidosAnuaisChart').getContext('2d');
    new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: @json($anos ?? [2022, 2023, 2024]),
            datasets: [{
                label: 'Total de Pedidos',
                data: @json($contagemAnual ?? [500, 750, 900]),
                backgroundColor: 'rgba(255, 159, 64, 0.7)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });
    
    // 5. Tendência do Valor Médio Mensal - AGORA COM FORMATAÇÃO
    var ctx5 = document.getElementById('valorMedioMensalChart').getContext('2d');
    new Chart(ctx5, {
        type: 'line',
        data: {
            labels: @json($meses),
            datasets: [{
                label: 'Valor Médio (R$)',
                data: @json($valorMedioMensal),
                borderColor: 'rgba(0, 123, 255, 1)',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderWidth: 2,
                fill: false,
                tension: 0.3
            }]
        },
        options: {
            scales: { 
                y: { 
                    beginAtZero: false,
                    // CALLBACK PARA FORMATAR O EIXO Y
                    ticks: {
                        callback: function(value, index, ticks) {
                            return formatarMoeda(value);
                        }
                    }
                } 
            },
            plugins: {
                // CALLBACK PARA FORMATAR O TOOLTIP
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += formatarMoeda(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

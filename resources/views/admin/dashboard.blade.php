@extends('admin.layout')
@section('titulo', 'Dashboard')
@section('conteudo')









  <!-- Dropdown Structure -->
  <ul id="dropdown2" class="dropdown-content">
    <li><a href="{{route('reserva.reservas')}}">Minhas reservas</a></li>
    @auth
      @if (auth()->user()->admin == 1)
        <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
      @endif 
    @endauth
    <li><a href="{{route('login.logout')}}">Sair</a></li>
  </ul>



  <nav class="red">
      <div class="nav-wrapper container ">
        <a href="#" class="brand-logo center">Dashboard</a>
        <a href="#" class="brand-logo" href="index.html">
          <img src="{{ asset('img/logo2.png') }}" style="width: 100px; height: auto; margin-left:80px">
      </a>
              <ul class="right ">                                 
            <li class="hide-on-med-and-down"><a href="#" onclick="fullScreen()"><i class="material-icons">settings_overscan</i> </a> </li>
            <li><a href="#" class="dropdown-trigger" data-target='dropdown2'> Olá {{auth()->user()->name}}  <i class="material-icons right">expand_more</i> </a></li>     
        </ul>
        <a href="#" data-target="slide-out" class="sidenav-trigger left  show-on-large"><i class="material-icons">menu</i></a>
      </div>
    </nav>

    


  <ul id="slide-out" class="sidenav " >
    <li><div class="user-view">
      <div class="background red ">
       <img src="{{asset('img/office.jpg')}}" style="opacity: 0.5"> 
      </div>
        {{-- <a href="#user"><img class="circle" src="{{asset('img/user.jpg')}}"></a> --}}
        <a href="#user"><img class="circle" src="https://upload.wikimedia.org/wikipedia/commons/a/a6/Anonymous_emblem.svg"></a>
        <a href="#name"><span class="white-text name"> {{auth()->user()->name }} </span></a>
        <a href="#email"><span class="white-text email"> {{auth()->user()->email}} </span></a>
     </div></li> 

      <li><a href="{{route('reserva.home')}}"><i class="material-icons">home</i>Home</a></li>

      <div class="user-view">
        <!-- Outros filtros e elementos -->

            <label for="date-filter">Filtrar por Data:</label>
            <input type="date" id="date-filter-start" name="startDate" placeholder="Data Início" value="{{ $startDate ? $startDate->format('Y-m-d') : ''}}">
            <input type="date" id="date-filter-end" name="endDate" placeholder="Data Fim" value="{{ $endDate ? $endDate->format('Y-m-d') : ''}}"><br><br>

            <div class="container-filtrar">
            <button id="apply-date-filter"  class="btn-filtrar">Aplicar Filtro</button> 
            <button id="clear-filters" class="btn-filtrar">Limpar Filtros</button>

            <button id="ControllerForaDoPrazo" class="btn-filtrar">Tabela Fora do Prazo</button>
          </div>
      </div>

  </ul>


  

  @auth
  @if (auth()->user()->admin == 1)

  <div class="row container">

    <section class="info">


      <div class="col s12 m4">
      <article class="bg-gradient-green card z-depth-4 ">
        <i class="material-icons">trending_up</i>
        <p>Reservas Realizadas</p>
        <h3>{{$total}}</h3>       
      </article>
      </div>

      <div class="col s12 m4">
        <article class="bg-gradient-blue card z-depth-4 ">
          <i class="material-icons">face</i>
          <p>Usuários</p>
          <h3>{{$usuarios}} </h3>           
        </article>
        </div>

        <div class="col s12 m4">
          <article class="bg-gradient-orange card z-depth-4 ">
            <i class="material-icons">close</i>
            <p>Cancelados</p>
            <h3>{{$totalCancelados}}</h3>            
          </article>
          </div>

          
    </section>        
  </div>


      <div class="row container ">
          <section class="graficos col s12 m6" >            
            <div class="grafico card z-depth-4">
                <h5 class="center"> Aquisição de usuários</h5>
                <canvas id="myChart10" width="400" height="200"></canvas>
            </div>           
          </section> 
          
          <section class="graficos col s12 m6">            
              <div class="grafico card z-depth-4">
                  <h5 class="center"> Reservas </h5>
              <canvas id="myChart3" width="400" height="200"></canvas> 
          </div>            
         </section>     
         
         <section>
          <div class="graficos col s12 m6">
            <div class="grafico card z-depth-4">
              <h5 class="center"> Reservas Realizadas</h5>
              <canvas id="myChart4" width="400" height="200"></canvas>
            </div>
          </div>
         </section>
         <section>
          <div class="graficos col s12 m6">
            <div class="grafico card z-depth-4">
              <h5 class="center"> Reservas Canceladas</h5>
              <canvas id="myChart5" width="400" height="200"></canvas>
            </div>
          </div><br><br><br><br>
         </section>

         <section>
          <div class="graficos col s12 m6">
            <div class="grafico card z-depth-4">
              <h5 class="center"> Fora do Prazo por Filial </h5>
              <canvas id="foraDoPrazoPorFilial" width="400" height="200"></canvas>
            </div>
          </div><br><br><br><br>
         </section>

         <section>
          <div class="graficos col s12 m6" >
            <div class="grafico card z-depth-4">
              <h5 class="center"> Fora do Prazo por Usuário </h5>
              <canvas id="foraDoPrazoPorUsuario" width="400" height="200"></canvas>
            </div>
          </div><br><br><br><br>
         </section>

   



<!-- Tabela de reservas fora do prazo -->
<div id="foraDoPrazoTable" class="container mt-4">
  <h2>Reservas Realizadas Fora do Prazo</h2>
  <table id="foraDoPrazoTableContent" class="table table-bordered table-striped">
      <thead class="thead-dark">
          <tr>
              <th>Tipo</th>
              <th>Passagem</th>
              <th>Origem</th>
              <th>Destino</th>
              <th>Pedido</th>
              <th>Viagem</th>
              <th>Solicitante</th>
              <th>Viajante</th>
              <th>Filial</th>
          </tr>
      </thead>
      <tbody>
          @forelse ($foraDoPrazoReservas as $reserva)
              <tr data-filial="{{ $reserva['filial'] }}" data-usuario="{{ $reserva['user_name'] }}">
                <td>{{ $reserva['tipo'] }}</td>
                <td>{{ $reserva['viagem'] }}</td>
                <td>{{ $reserva['origem'] }}</td>
                <td>{{ $reserva['destino'] }}</td>
                <td>{{ \Carbon\Carbon::parse($reserva['created_at'])->format('d/m/Y ') }}</td>
                <td>{{ \Carbon\Carbon::parse($reserva['ida'])->format('d/m/Y') }}</td>
                <td>{{ $reserva['user_name'] }}</td>
                <td>{{ $reserva['nome'] }}</td>
                <td>{{ $reserva['filial'] }}</td>
              </tr>
          @empty
              <tr>
                  <td colspan="8">Nenhuma reserva fora do prazo encontrada.</td>
              </tr>
          @endforelse
      </tbody>
  </table>
</div>
<br><br>



</div>

</div>


@endsection

@push('graficos')
<script>
/* Gráfico 01 */
var ctx = document.getElementById('myChart10');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [{{$usermonth}}],
        datasets: [{
            label: [{!! $userLabel !!}],
            data: [ {{$userTotal}} ],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',                         
                'rgba(255, 159, 64, 1)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',                     
                'rgba(255, 159, 64, 1)'
            ],
           borderWidth: 1, 
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});


/* Gráfico 02 */
var ctx = document.getElementById('myChart3');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Passagens', 'Veiculos', 'Hospedagens', 'Adiantamentos'],
        datasets: [{
            label: 'Total de Reservas',
            data: [{{$passagens}}, {{$veiculos}}, {{$hospedagens}}, {{$adiantamentos}}],
            backgroundColor: [
              'rgba(255, 0, 0)',
              'rgba(54, 162, 235)',                         
                'rgba(234, 239, 44)',
                'rgba(0, 255, 0)'
            ]
        }]
    }
});

/* Gráfico 03 */
var ctx = document.getElementById('myChart4').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Realizadas no Prazo', 'Realizadas fora do Prazo'],
        datasets: [{
            label: 'Visitas',
            data: [{{$prazo}}, {{$fora_do_prazo}}],
            backgroundColor: [
                'rgba(0, 255, 0)',                         
                'rgba(255, 0, 0)',
            ]
        }]
    }
});

/* Gráfico 03 */
var ctx = document.getElementById('myChart5').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Passagens', 'Veiculos', 'Hospedagens', 'Adiantamentos'],
        datasets: [{
            label: 'Visitas',
            data: [{{$passagensCanceladas}}, {{$veiculosCancelados}}, {{$hospedagensCanceladas}}, {{$adiantamentosCancelados}}],
            backgroundColor: [
              'rgba(255, 0, 0)',
              'rgba(54, 162, 235)',                         
              'rgba(234, 239, 44)',
                'rgba(0, 255, 0)'
            ]
        }]
    }
});

 
// FORA DO PRAZO POR FILIAL
var ctx1 = document.getElementById('foraDoPrazoPorFilial').getContext('2d');
    var foraDoPrazoPorFilialChart  = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: {!! json_encode($filiais) !!},
            datasets: [{
                label: 'Total Fora do Prazo por Filial',
                data: {!! json_encode($foraDoPrazoPorFilialData) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.5)'
            }]
        },
        options: {
        onClick: (e, activeElements) => {
            if (activeElements.length > 0) {
                const datasetIndex = activeElements[0].datasetIndex;
                const index = activeElements[0].index;
                const filial = foraDoPrazoPorFilialChart.data.labels[index];
                filterTableByFilial(filial);
            }
        }
    }
});


    var ctx2  = document.getElementById('foraDoPrazoPorUsuario').getContext('2d');
    var foraDoPrazoPorUsuarioChart  = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: {!! json_encode($colaboradores) !!},
            datasets: [{
                label: 'Total Fora do Prazo por Usuário',
                data: {!! json_encode($foraDoPrazoPorColaboradorData) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        },
        options: {
        onClick: (e, activeElements) => {
            if (activeElements.length > 0) {
                const datasetIndex = activeElements[0].datasetIndex;
                const index = activeElements[0].index;
                const user = foraDoPrazoPorUsuarioChart.data.labels[index];
                filterTableByUser(user);
            }
        }
    }
});


    // Adiciona o evento de clique ao gráfico 3
    document.getElementById('ControllerForaDoPrazo').addEventListener('click', function() {
        document.getElementById('foraDoPrazoTable').style.display = document.getElementById('foraDoPrazoTable').style.display === 'none' ? 'block' : 'none'; // Exibe ou oculta a tabela


    });

    document.getElementById('apply-date-filter').addEventListener('click', function() {
        var startDate = document.getElementById('date-filter-start').value;
        var endDate = document.getElementById('date-filter-end').value;
        window.location.href = `?startDate=${startDate}&endDate=${endDate}`;
    });

    document.addEventListener('DOMContentLoaded', function() {
        const clearButton = document.getElementById('clear-filters');

        clearButton.addEventListener('click', function() {
            // Limpa os campos de data
            document.getElementById('date-filter-start').value = '';
            document.getElementById('date-filter-end').value = '';
            
            // Adicione aqui qualquer outra lógica que você queira para limpar os filtros
            // Por exemplo, você pode enviar uma solicitação para redefinir os filtros no servidor ou atualizar a exibição dos dados.

        });
        
    });
    
</script>
    
@endpush

@else
<br><br>
<h1> <center>Você não tem permissão para acessar essa página </center></h1>

@endif
@endauth

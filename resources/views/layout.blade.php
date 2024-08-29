<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>    
    .admin{
      /* Modificar a cor do texto*/
      color: #ffffff;
      background-color: #ff0000;
    }
    .custom-image {
        width: 250px;
        height: 250px;
    }

    .custom-image2 {
      width: 100%; /* Ajusta a imagem para ocupar 100% da largura do container */
      height: auto; /* Mantém a proporção da imagem */
      /* Se você quiser um tamanho específico, defina largura e altura diretamente */
      max-width: 728px; /* Define a largura máxima da imagem */
      max-height: 365px; /* Define a altura máxima da imagem */
    }

    .btn-cadastrar {
    background-color: #ff0000; /* Cor de fundo do botão */
    color: white; /* Cor do texto */
    padding: 10px 20px; /* Espaçamento interno */
    border: none; /* Remover bordas */
    border-radius: 5px; /* Bordas arredondadas */
    cursor: pointer; /* Cursor de mãozinha ao passar o mouse */
    text-align: center; /* Centralizar texto */
    text-decoration: none; /* Remover sublinhado */
    display: inline-block; /* Mostrar como bloco inline */
    font-size: 16px; /* Tamanho da fonte */
}

.btn-cadastrar:hover {
    background-color: #0056b3; /* Cor de fundo ao passar o mouse */
}

.btn-group .btn.active {
    background-color: #ff0000; /* Cor de destaque para o botão ativo */
    color: white;
}


    
    </style>


</head>
<body>

    <!-- Dropdown Structure -->
    <ul id='dropdown1' class='dropdown-content'>

    </ul>
  

  <!-- Dropdown Structure -->
  <ul id="dropdown2" class="dropdown-content">
    <li><a href="{{route('reserva.reservas')}}">Minhas reservas</a></li>
    @auth
      @if (auth()->user()->admin == 1)
        <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li><a href="{{route('admin.canceladas')}}">Canceladas</a></li>
      @endif 
    @endauth
    <li><a href="{{route('reserva.sobre')}}">Sobre</a></li>
    <li><a href="{{route('login.logout')}}">Sair</a></li>
</ul>

    
  <nav class="red">
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo center">Reserva - Solicitações</a>
      <a href="#" class="brand-logo" href="index.html">
        <img src="{{ asset('img/logo2.png') }}" style="width: 100px; height: auto; margin-left:80px">
    </a>
      <ul id="nav-mobile" class="left">
        <a href="#" data-target="slide-out" class="sidenav-trigger left  show-on-large"><i class="material-icons">menu</i></a>
      </ul>

      <ul id="nav-mobile" class="brand-logo center">
        <li class="hide-on-med-and-down">
          <i class="material-icons left" style="margin-left:400px">visibility</i>{{ \Illuminate\Support\Facades\DB::table('sessions')->where('user_id','!=',null)->count() }}
        </li>
      </ul>

      @auth
      <ul id="nav-mobile" class="right">
        <li><a href="" class="dropdown-trigger" data-target='dropdown2'> Olá {{auth()->user()->name}} <i class="material-icons right">expand_more</i></a></li>
      </ul>
    @else
      <ul id="nav-mobile" class="right">
        <li><a href="{{route('login.form')}}">Login <i class="material-icons right">lock</i></a></li>
      </ul>
    @endauth

      @if (@auth()->user()->id != null)
        
      <ul id="slide-out" class="sidenav">
        <li>
            <div class="user-view">
                <div class="background red">
                    <img src="{{asset('img/office.jpg')}}" style="opacity: 0.5"> 
                </div>
                <a href="#user"><img class="circle" src="https://upload.wikimedia.org/wikipedia/commons/a/a6/Anonymous_emblem.svg"></a>
                <a href="#name"><span class="white-text name"> {{auth()->user()->name }} </span></a>
                <a href="#email"><span class="white-text email"> {{auth()->user()->email}} </span></a>
                <a href="#filial"><span class="white-text filial"> {{auth()->user()->filial}} </span></a>
            </div>
        </li> 
        <li><a href="{{route('reserva.home')}}"><i class="material-icons">home</i>Home</a></li>
        <li><a href="{{route('reserva.reservas')}}"><i class="material-icons">description</i>Minhas Reservas</a></li>
        <li><a href="{{route('reserva.passagem-aerea')}}"><i class="material-icons">flight</i>Reservar Passagem</a></li>
        <li><a href="{{route('reserva.veiculo')}}"><i class="material-icons">directions_car</i>Reservar Veiculo</a></li>
        <li><a href="{{route('reserva.hospedagem')}}"><i class="material-icons">hotel</i>Reservar Hospedagem</a></li>
        <li><a href="{{route('reserva.adiantamento')}}"><i class="material-icons">attach_money</i>Solicitar Adiantamento</a></li>
        <li><a href="{{route('reserva.sobre')}}"><i class="material-icons">help</i>Sobre</a></li>
    </ul>
    @endif



    </div>
  </nav>

@yield('conteudo')
    <!-- Compiled and minified JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elemsDropdown = document.querySelectorAll('.dropdown-trigger');
            M.Dropdown.init(elemsDropdown, { coverTrigger: false, constrainWidth: false });

            var elemsSidenav = document.querySelectorAll('#slide-out');
            var instancesSidenav = M.Sidenav.init(elemsSidenav, { edge: 'left' });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="{{asset('js/chart.js')}}" ></script>
    <script src="{{asset('js/main.js')}}"></script>
    @stack('graficos')

</body>
</html>
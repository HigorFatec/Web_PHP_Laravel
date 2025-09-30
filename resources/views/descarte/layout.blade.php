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

    body{
      font-family: 'Montserrat', sans-serif !important;
    }

    .row{
          background-color: #777777; /* Azul claro */
          padding: 20px;
        }

    .red{
      background-color: #184693 !important;
    }    
    .admin{
      /* Modificar a cor do texto*/
      color: #ffffff;
      background-color: #ff0000;
    }
    .custom-image {
    display: block;
    margin-left: auto;
    margin-right: auto;
              /* Se você quiser um tamanho específico, defina largura e altura diretamente */
      max-width: 75px; /* Define a largura máxima da imagem */
      max-height: 75px; /* Define a altura máxima da imagem */
    }


    .custom-image2 {
      width: auto; /* Ajusta a imagem para ocupar 100% da largura do container */
      height: auto; /* Mantém a proporção da imagem */
      /* Se você quiser um tamanho específico, defina largura e altura diretamente */
      max-width: 2050px; /* Define a largura máxima da imagem */
      max-height: 400px; /* Define a altura máxima da imagem */
    }

    .btn-cadastrar {
    background-color: #184693; /* Cor de fundo do botão */
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


.cards-container {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 20px; /* espaço entre os cards */
}
.cards-container .card {
  width: 200px; /* ou ajuste conforme seu layout */
}

.extra-margin-bottom {
  margin-bottom: 40px; /* ajuste a altura desejada */
}

.rounded-card {
  border-radius: 12px;
  overflow: hidden; /* Importante para que imagens dentro também fiquem arredondadas */
}


  .btn {
    background-color: black;
    color: white;
    padding: 0px 20px;
    border: none;
  }

  .btn.active {
    background-color: blue;
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
        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiYmNkNDJhZDQtMjk3NS00MDk0LWFhYmYtMzFhNDFmZjI4ZDIwIiwidCI6IjE5MDE3MzlkLTg1M2YtNDkwMS1iMTYwLTYxMDY4NWMwYzc5ZSJ9">Power BI</a></li>
        <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li><a href="{{route('admin.canceladas')}}">Canceladas</a></li>
        <li><a href="{{route('admin.finalizadas')}}">Finalizadas</a></li>
      @endif 
    @endauth
    <li><a href="{{route('reserva.sobre')}}">Sobre</a></li>
    <li><a href="{{route('login.logout')}}">Sair</a></li>
</ul>

    
  <nav class="red">
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo center">Descarte de Pneus</a>
      <a href="#" class="brand-logo" href="index.html">
        <img src="{{ asset('img/LogoSite.png') }}" style="width: 100px; height: auto; margin:10px;margin-left:80px">
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
        <li><a href="{{route('descarte.index')}}">Login <i class="material-icons right">lock</i></a></li>
      </ul>
    @endauth

      @if (@auth()->user()->id != null)
        
      <ul id="slide-out" class="sidenav">
        <li>
            <div class="user-view">
                <div class="background red">
                    <img src="{{asset('img/office2.jpg')}}" style="opacity: 0.5"> 
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
      function disableButtonOnClick(button) {
          // Desativa o botão para evitar múltiplos cliques
          button.disabled = true;
          // Opcional: Alterar o texto do botão para indicar o envio
          button.textContent = 'Enviando...';
          // Retorna true para permitir o envio do formulário
          return true;
      }
  </script>


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

    <script>
document.getElementById('form-upload').addEventListener('submit', async function(e) {
    e.preventDefault();

    const inputs = ['foto_n_fogo', 'foto_dot', 'foto_descarte'];
    const formData = new FormData();

    for (const id of inputs) {
        const input = document.getElementById(id);
        if (input.files[0]) {
            const file = input.files[0];

            // Reduz qualidade usando Canvas
            const img = new Image();
            img.src = URL.createObjectURL(file);
            await new Promise(resolve => img.onload = resolve);

            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            // Converte para blob com qualidade 50%
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.5));

            formData.append(id, blob, file.name);
        }
    }

    // Agora você pode enviar via fetch para o servidor
    fetch('/upload', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(res => alert('Upload concluído!'))
        .catch(err => console.error(err));
});
</script>


    @stack('graficos')

</body>
</html>
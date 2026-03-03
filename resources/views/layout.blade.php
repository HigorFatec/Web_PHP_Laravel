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

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- JS Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>    

    /* Apenas para login */
    .card-blue {
        background: linear-gradient(135deg, #0055aa, #003366) !important;
        color: white;
        border-radius: 20px;
    }

    .card-title-login {
    color: #ffffff !important;
    }

    .btn-cadastrar-2 {
        background-color: #ffffff;
        color: #184693;
        padding: 10px 20px; /* Espaçamento interno */
        border: none; /* Remover bordas */
        border-radius: 5px; /* Bordas arredondadas */
        cursor: pointer; /* Cursor de mãozinha ao passar o mouse */
        text-align: center; /* Centralizar texto */
        text-decoration: none; /* Remover sublinhado */
        display: inline-block; /* Mostrar como bloco inline */
        font-size: 16px; /* Tamanho da fonte */
    }

    .btn-cadastrar-2:hover {
        background-color: #dbe6ff;
        color: #184693;
    }
    
    /* Isso força o corpo da página a ter no mínimo a altura da tela */
    html, body {
        height: 100%;
        margin: 0;
    }


    body{
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* Ocupa 100% da altura da visualização */

      font-family: 'Montserrat', sans-serif !important;
      background-color: #f4f7fa !important; /* Fundo cinza claro suave */
    }

    /* O segredo está aqui: o main vai ocupar todo o espaço sobrando */
    main {
        flex: 1 0 auto;
    }


    .blue{
      background: linear-gradient(135deg, #0055aa, #003366) !important;
    }    

    .row{
          margin-bottom: 0; /* Ajuste para melhor espaçamento se necessário */
          padding: 20px;
    }

    /* Estilo para o Contêiner da Opção de Fornecedor (o card branco) */
    .supplier-card {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Sombra mais sutil */
        text-align: center;
        max-width: 500px; /* Limita a largura para centralizar melhor */
        margin: 80px auto; /* Centraliza verticalmente e horizontalmente */
    }
    /* Estilos para os botões de seleção de fornecedor (Melhora a UX) */
    .btn-supplier-type {
        margin: 5px;
        transition: background-color 0.3s, box-shadow 0.3s;
        font-weight: 500;
        text-transform: none; /* Deixa o texto normal */
    }
    .btn {
      background-color: #cccccc;
    }

    .btn-supplier-type.active {
        background-color: #26a69a !important; /* Materialize Teal (verde de destaque) */
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
    }

    .btn-supplier-type:not(.active) {
        background-color: #cccccc !important; /* Cor suave para desativado */
        color: #333333 !important;
    }

    /* Estilo para o botão Enviar principal */
    .btn-submit-main {
        background-color: #184693 !important; /* Azul Marinho Principal */
        margin-top: 20px;
        padding: 0 30px;
        height: 45px;
        line-height: 45px;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .btn-submit-main:hover {
        background-color: #0d2c61 !important; /* Tom mais escuro no hover */
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
    background-color: #0056b3; /* Cor de destaque para o botão ativo */
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


  input[readonly] {
    background-color: #f0f0f0; /* fundo cinza claro */
    color: #555;               /* texto levemente escuro */
    border: 1px solid #ccc;    /* borda suave */
    cursor: not-allowed;       /* cursor de bloqueio */
  }

  input[readonly]:focus {
    outline: none;             /* remove brilho ao focar */
  }





/* --- WRAPPERS E ESTRUTURA --- */
    .login-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    /* --- CARDS --- */
    /* Card Branco (Usado no Cadastro e Fornecedor) */
    .card-login {
        border-radius: 20px !important;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        background: #fff !important;
        color: #333 !important; /* Garante texto escuro no card branco */
    }

    .login-title {
        color: #003366;
        font-weight: 800;
        margin-bottom: 5px;
        font-size: 2rem;
    }

    .login-subtitle {
        color: #777;
        font-size: 0.95rem;
        margin-bottom: 20px;
    }

    /* --- FORMULÁRIOS E ÍCONES --- */
    .input-field .prefix.icon-blue {
        color: #003366 !important;
    }

    .input-field input:focus {
        border-bottom: 2px solid #005bb6 !important;
        box-shadow: 0 1px 0 0 #005bb6 !important;
    }

    .input-field input:focus + label {
        color: #005bb6 !important;
    }

    /* --- BOTÕES --- */
    .btn-login {
        background-color: #003366 !important;
        height: 45px !important;
        line-height: 45px !important;
        border-radius: 8px !important;
        padding: 0 25px !important;
        font-weight: 600 !important;
        text-transform: uppercase;
    }

    .btn-back {
        color: #777 !important;
        font-weight: 600;
        text-transform: none;
    }

    .btn-back:hover {
        background: rgba(0,0,0,0.05) !important;
        color: #333 !important;
    }

    .shadow-btn {
        box-shadow: 0 4px 14px 0 rgba(0, 51, 102, 0.39) !important;
    }

    /* --- BOX DE ERRO --- */
    .error-box {
        background: #fff5f5;
        border-left: 5px solid #ff5252;
        padding: 15px;
        margin-bottom: 20px;
        color: #d32f2f;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    /* --- UTILITÁRIOS --- */
    .mt-2 { margin-top: 2rem; }
    .mb-2 { margin-bottom: 2rem; }
    .ml-1 { margin-left: 8px; }


    /* --- ESTILOS EXCLUSIVOS DO LOGIN --- */
.btn-microsoft {
    background-color: #2F2F2F !important;
    color: #fff !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
    gap: 10px;
    height: 45px !important;
    line-height: 45px !important;
    border-radius: 8px !important;
    text-transform: none !important;
    font-weight: 500 !important;
}

.btn-microsoft img {
    width: 20px;
}

.btn-microsoft:hover {
    background-color: #1f1f1f !important;
}

.full-width {
    width: 100% !important;
}

.divider-text {
    display: flex;
    align-items: center;
    text-align: center;
    color: #777;
    margin: 20px 0;
}

.divider-text::before, .divider-text::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #ddd;
}

.divider-text:not(:empty)::before { margin-right: .5em; }
.divider-text:not(:empty)::after { margin-left: .5em; }

/* Ajuste para garantir que labels e inputs de login (fundo branco) fiquem visíveis */
.card-login .input-field input {
    color: #333 !important;
}


    /* FIM */



/* Estilização do Rodapé de Suporte */
.page-footer-custom {
    background-color: #ffffff !important;
    border-top: 1px solid #e0e0e0;
    padding: 10px 0; /* Reduzido de 20px para 10px */
    color: #777;
    flex-shrink: 0;
}

.support-link-slim {
    display: inline-flex;
    align-items: center;
    color: #184693 !important;
    font-weight: 500;
    text-decoration: none;
    font-size: 0.85rem;
    transition: opacity 0.3s;
}

.support-link-slim:hover {
    opacity: 0.7;
    text-decoration: underline;
}

.support-link-slim i {
    margin-right: 5px;
    font-size: 1.1rem;
}


nav .nav-wrapper i {
    height: 64px;
    line-height: 64px;
}
.dropdown-content {
    top: 64px !important; /* Garante que o dropdown não cubra o nav */
}
/* Padding para sub-itens do menu para criar hierarquia visual */
.sidenav .collapsible-body li a {
    padding-left: 54px !important;
    font-size: 13px;
}

/* Deixa os títulos dos setores (Suprimentos, Fiscal...) mais destacados */
.sidenav .subheader {
    color: #184693 !important;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 1px;
}

/* Alinhamento dos ícones */
.sidenav li > a > i.material-icons {
    margin-right: 20px;
}


    


  /* --- Estilos do Mini Chat --- */
#mini-chat-container {
    position: fixed !important;
    bottom: 20px !important;
    right: 20px !important;
    width: 300px !important; /* Largura quando aberto */
    height: 400px !important; /* Altura quando aberto */
    background-color: #ffffff !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
    z-index: 1000 !important; /* Garante que fique acima */
    display: flex !important;
    flex-direction: column !important;
    transition: all 0.3s ease !important;
    overflow: hidden !important;
}

#mini-chat-container.chat-closed {
    height: 50px !important; /* Altura quando fechado */
    width: 250px !important; 
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

#chat-header {
    background-color: #0055aa !important; /* Cor do cabeçalho */
    color: white !important;
    padding: 15px !important;
    font-weight: 600 !important;
    cursor: pointer !important; 
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    height: 50px !important;
    box-sizing: border-box !important;
}

#toggle-chat-button {
    background: none !important;
    border: none !important;
    color: white !important;
    font-size: 16px !important;
    cursor: pointer !important;
    padding: 0 !important;
    line-height: 1 !important;
    transition: transform 0.3s ease !important;
}

.chat-toggle-icon {
    display: block !important;
}

#mini-chat-container.chat-closed #chat-header {
    border-radius: 10px !important;
}

#mini-chat-container.chat-closed .chat-toggle-icon {
    transform: rotate(0deg) !important; /* Ícone ◀ quando fechado */
}
#mini-chat-container:not(.chat-closed) .chat-toggle-icon {
    transform: rotate(90deg) !important; /* Ícone ▼ quando aberto */
}

#chat-body {
    flex-grow: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

#mini-chat-container.chat-closed #chat-body {
    display: none !important; /* Oculta o corpo quando fechado */
}

.chat-messages {
    padding: 10px !important;
    overflow-y: auto !important;
    flex-grow: 1 !important;
    border-bottom: 1px solid #eee !important;
}

.chat-input {
    display: flex !important;
    padding: 10px !important;
    border-top: 1px solid #ddd !important;
}

.chat-input input {
    flex-grow: 1 !important;
    padding: 8px !important;
    border: 1px solid #ccc !important;
    border-radius: 4px !important;
    margin-right: 5px !important;
}

.chat-input button {
    background-color: #0055aa !important;
    color: white !important;
    border: none !important;
    padding: 8px 12px !important;
    border-radius: 4px !important;
    cursor: pointer !important;
}
/* Fim dos Estilos do Mini Chat */

    </style>


</head>
<body>

    <!-- Dropdown Structure -->
    <ul id='dropdown1' class='dropdown-content'>

    </ul>
  

  <!-- Dropdown Structure -->
  <ul id="dropdown2" class="dropdown-content">
    {{-- <li><a href="{{route('reserva.reservas')}}">Minhas reservas</a></li>
    <li><a href="{{route('reserva.pendentes')}}">Reservas Pendentes</a></li>
    @auth
      @if (auth()->user()->admin == 1)
        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiYmNkNDJhZDQtMjk3NS00MDk0LWFhYmYtMzFhNDFmZjI4ZDIwIiwidCI6IjE5MDE3MzlkLTg1M2YtNDkwMS1iMTYwLTYxMDY4NWMwYzc5ZSJ9">Power BI</a></li>
        <li><a href="{{route('admin.dashboard')}}">Dashboard Viagens</a></li>
        <li><a href="{{route('admin.canceladas')}}">Viagens Canceladas</a></li>
        <li><a href="{{route('admin.finalizadas')}}">Viagens Finalizadas</a></li>
      @endif 
      @if (auth()->user()->admin == 3 || auth()->user()->admin == 4)
        <li><a href="{{route('fiscal.aprovacao')}}">Pedidos Fiscais</a></li>
        
        <li>
          <form action="{{ route('exportar.fiscais') }}" method="GET" style="margin: 0; padding: 0;">
            <button type="submit" class="btn green" style="width: 100%; text-align: left;">Exportar Dados</button>
          </form>
        </li>
                <li><a href="{{route('empresa.aprovacao')}}">Fornecedores Pendentes</a></li>

      @elseif (auth()->user()->admin == 5)
        <li><a href="{{route('financeiro_fr.saldo')}}">Ajustar Saldo</a></li>
        <li><a href="{{route('admin.financeiro-dashboard')}}">Dashboard Financeiro</a></li> 

      @elseif (auth()->user()->admin == 100)
        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiYmNkNDJhZDQtMjk3NS00MDk0LWFhYmYtMzFhNDFmZjI4ZDIwIiwidCI6IjE5MDE3MzlkLTg1M2YtNDkwMS1iMTYwLTYxMDY4NWMwYzc5ZSJ9">Power BI</a></li>
        <li><a href="{{route('admin.dashboard')}}">Dashboard Viagens</a></li>
        <li><a href="{{route('admin.canceladas')}}">Viagens Canceladas</a></li>
        <li><a href="{{route('admin.finalizadas')}}">Viagens Finalizadas</a></li>
        <li><a href="{{route('fiscal.aprovacao')}}">Pedidos Fiscais</a></li>
        <li><a href="{{route('financeiro_fr.saldo')}}">Ajustar Saldo</a></li>
        <li><a href="{{route('admin.financeiro-dashboard')}}">Dashboard Financeiro</a></li> 
        <li><a href="{{route('empresa.aprovacao')}}">Fornecedores Pendentes</a></li>

      @endif

    @endauth
    <li><a href="{{route('reserva.sobre')}}">Sobre</a></li> --}}
    <li><a href="{{route('login.logout')}}">Sair</a></li>
</ul>



<nav class="blue">
  <div class="nav-wrapper container" style="display: flex; align-items: center; justify-content: space-between;">
    
    <div style="display: flex; align-items: center;">
      @auth
        <a href="#" data-target="slide-out" class="sidenav-trigger show-on-large" style="display: block; margin: 0 15px 0 0;"><i class="material-icons">menu</i></a>
      @else
        <a href="{{route('login.form')}}" style="display: block; margin: 0 15px 0 0;"><i class="material-icons">menu</i></a>
      @endauth

      <a href="{{route('index')}}" style="display: flex; align-items: center;">
        <img src="{{ asset('img/LogoSite.png') }}" style="width: 80px; height: auto;">
      </a>
    </div>

    <ul class="right" style="display: flex; margin: 0;">
      <li style="position: relative;">
        <a href="#!" class="dropdown-trigger" data-target="dropdown-notif" style="display: flex; align-items: center; padding: 0 15px;">
          <i class="material-icons">notifications</i>
          <span id="notif-badge" class="new badge red" data-badge-caption="" 
                style="display:none; position: absolute; top: 10px; right: 5px; min-width: 18px; height: 18px; line-height: 18px; font-size: 10px; padding: 0;">
            0
          </span>
        </a>
      </li>

      @auth
        <li>
          <a href="#!" class="dropdown-trigger" data-target='dropdown2' style="display: flex; align-items: center;">
            <span class="hide-on-small-only">Olá {{auth()->user()->name}}</span>
            <i class="material-icons right">expand_more</i>
          </a>
        </li>
    @else
    <li>
        <a href="{{route('login.form')}}" style="display: flex; align-items: center;">
        Login <i class="material-icons right">lock</i>
        </a>
    </li>
    @endauth




@auth
<ul id="slide-out" class="sidenav">
    <li>
        <div class="user-view" style="padding: 30px 32px 20px;">
            <div class="background" style="background: #184693;">
                <img src="{{asset('img/office2.jpg')}}" style="opacity: 0.2; width: 100%; height: 100%; object-fit: cover;">
            </div>
            <a href="#user"><img class="circle shadow" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=fff&color=184693"></a>
            <span class="white-text name" style="font-weight: 600;">{{ auth()->user()->name }}</span>
            <span class="white-text email" style="font-size: 11px; opacity: 0.8;">{{ auth()->user()->email }}</span>
            <span class="white-text unidade"  style="font-weight: 600; opacity: 0.8;">{{ auth()->user()->filial }}</span>
        </div>
    </li>

    <li><a class="waves-effect" href="{{route('index')}}"><i class="material-icons blue-text text-darken-4">home</i>Início</a></li>

    <li><div class="divider"></div></li>
    <li><a class="subheader">Suprimentos</a></li>
    
    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="collapsible-header waves-effect">
                    <i class="material-icons orange-text">luggage</i>Portal de Reservas
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="{{route('reserva.home')}}">Solicitar Reserva</a></li>
                        <li><a href="{{route('reserva.reservas')}}">Minhas Reservas</a></li>
                        <li><a href="{{route('reserva.pendentes')}}">Pendentes</a></li>
                    </ul>
                </div>
            </li>

            @if(in_array(auth()->user()->admin, [1, 100]))
            <li>
                <a class="collapsible-header waves-effect">
                    <i class="material-icons blue-text">assessment</i>Gestão Viagens
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="{{route('admin.dashboard')}}">Painel de Controle</a></li>
                        <li><a href="{{route('admin.finalizadas')}}">Viagens Finalizadas</a></li>
                        <li><a href="{{route('admin.canceladas')}}">Viagens Canceladas</a></li>
                    </ul>
                </div>
            </li>
            @endif
        </ul>
    </li>

    @if(in_array(auth()->user()->admin, [3, 4, 100]))
    <li><a class="waves-effect" href="{{route('empresa.aprovacao')}}"><i class="material-icons teal-text">storefront</i>Fornecedores</a></li>
    @endif

    @if(in_array(auth()->user()->admin, [3, 4, 100]))
    <li><div class="divider"></div></li>
    <li><a class="subheader">Fiscal</a></li>
    <li><a class="waves-effect" href="{{route('fiscal.aprovacao')}}"><i class="material-icons teal-text">fact_check</i>Pedidos Fiscais</a></li>
    <li>
        <a class="waves-effect" href="#" onclick="event.preventDefault(); document.getElementById('export-form').submit();">
            <i class="material-icons green-text">file_download</i>Exportar Dados
        </a>
        <form id="export-form" action="{{ route('exportar.fiscais') }}" method="GET" style="display: none;"></form>
    </li>
    @endif

    @if(in_array(auth()->user()->admin, [5, 100]))
    <li><div class="divider"></div></li>
    <li><a class="subheader">Financeiro</a></li>
    <li><a class="waves-effect" href="{{route('financeiro_fr.saldo')}}"><i class="material-icons amber-text text-darken-3">account_balance_wallet</i>Ajustar Saldos e Gestores</a></li>
    <li><a class="waves-effect" href="{{route('admin.financeiro-dashboard')}}"><i class="material-icons amber-text text-darken-3">insert_chart</i>Gestão Financeira</a></li>
    @endif

    <li><div class="divider"></div></li>
    <li><a class="waves-effect grey-text" href="{{route('reserva.sobre')}}"><i class="material-icons">info_outline</i>Sobre</a></li>
    <li><a class="waves-effect red-text" href="{{route('login.logout')}}"><i class="material-icons red-text">power_settings_new</i>Sair</a></li>
</ul>
@endauth







  </div>
</nav>

<ul id="dropdown-notif" class="dropdown-content" style="min-width: 300px; max-height: 400px;">
  <li><span class="blue-text" style="font-weight:bold; display: block; padding: 15px;">Notificações</span></li>
  <li class="divider"></li>
  <div id="notif-lista">
    <li><a href="#!" class="grey-text center">Carregando...</a></li>
  </div>
</ul>

  <main>

@yield('conteudo')

</main>
    <!-- Compiled and minified JavaScript -->

        <script>
      function setConferenciaPneus(button) {
          // Define o valor no campo oculto com base no botão clicado
          document.getElementById('conferencia_pneus').value = button.getAttribute('data-value');
      }
  </script>


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

   {{-- ESTRUTURA CHAT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seletores de elementos
        const chatContainer = document.getElementById('mini-chat-container');
        const chatHeader = document.getElementById('chat-header');
        const toggleButton = document.getElementById('toggle-chat-button');
        const messagesDiv = document.querySelector('.chat-messages');
        const chatInput = document.querySelector('.chat-input input');
        const sendButton = document.querySelector('.chat-input button');
        
        // Variável de controle do Polling (Rastreia a última mensagem vista)
        let lastTimestamp = null; 
        
        // --- 1. Funcionalidade de Toggle (Mostrar/Ocultar) ---
        function toggleChat() {
            chatContainer.classList.toggle('chat-closed');
            const icon = toggleButton.querySelector('.chat-toggle-icon');
            if (chatContainer.classList.contains('chat-closed')) {
                icon.innerHTML = '◀'; 
                toggleButton.setAttribute('aria-label', 'Abrir Chat');
            } else {
                icon.innerHTML = '▼';
                toggleButton.setAttribute('aria-label', 'Ocultar Chat');
                // Força a busca ao abrir (importante para evitar desatualização)
                fetchNewMessages(); 
            }
        }

        toggleButton.addEventListener('click', toggleChat);
        chatHeader.addEventListener('click', function(event) {
            if (event.target.id !== 'toggle-chat-button' && !toggleButton.contains(event.target)) {
                 toggleChat();
            }
        });
        
        // Inicia o chat fechado e o ícone para abrir
        chatContainer.classList.add('chat-closed');
        toggleButton.querySelector('.chat-toggle-icon').innerHTML = '◀';
        
        // --- 2. Funcionalidade de Envio e Recebimento (AJAX Polling) ---

        // Função auxiliar para adicionar mensagens ao DOM
        function appendMessage(user, content, isSelf = false) {
            const newMessage = document.createElement('p');
            newMessage.innerHTML = `<strong>${user}:</strong> ${content}`;
            
            if (isSelf) {
                 newMessage.style.textAlign = 'right'; 
                 newMessage.style.color = '#0055aa'; 
            } else {
                 newMessage.style.textAlign = 'left'; 
                 newMessage.style.color = '#333'; 
            }
            
            messagesDiv.appendChild(newMessage);
            messagesDiv.scrollTop = messagesDiv.scrollHeight; 
        }

        // Função para ENVIAR mensagens
        function sendMessage() {
            const content = chatInput.value.trim();
            if (content === '') return;

            sendButton.disabled = true;

            fetch('{{ route("chat.send") }}', { // Rota Laravel: chat.send
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    appendMessage(data.user_name, data.message.content, true);
                    chatInput.value = ''; 
                    //lastTimestamp = data.message.created_at; 
                } else {
                    alert('Erro ao enviar mensagem.');
                }
            })
            .catch(error => console.error('Erro no envio:', error))
            .finally(() => {
                sendButton.disabled = false;
            });
        }
        
        sendButton.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                sendMessage();
            }
        });

        // Função para BUSCAR mensagens (Polling)
        function fetchNewMessages() {
            // Só faz polling se o chat estiver aberto OU se for o carregamento inicial (lastTimestamp é nulo)
            if (chatContainer.classList.contains('chat-closed') && lastTimestamp !== null) {
                return;
            }
            
            // Variável de controle para saber se é o primeiro carregamento
            const isInitialLoad = (lastTimestamp === null);

            fetch(`{{ route("chat.fetch") }}?last_timestamp=${lastTimestamp}`) // Rota Laravel: chat.fetch
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na requisição de chat: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    
                    // --- CORREÇÃO DE HISTÓRICO: Limpa TUDO no carregamento inicial ---
                    if (isInitialLoad) {
                        messagesDiv.innerHTML = ''; 
                    }
                    // ------------------------------------------------------------------

                    data.messages.forEach(msg => {
                        // O '{{ Auth::id() }}' é o ID do usuário logado na sessão Laravel
                        const isSelf = msg.user_id == '{{ Auth::id() }}'; 
                        const userName = msg.user ? msg.user.name : 'Desconhecido';
                        
                        appendMessage(userName, msg.content, isSelf);
                    });
                    
                    lastTimestamp = data.last_timestamp;
                } 
                // Ajuste extra: Se for o carregamento inicial e não houver mensagens, limpe o "Carregando"
                else if (isInitialLoad) {
                     messagesDiv.innerHTML = '<p class="system-message">Nenhuma mensagem ainda. Comece a conversar!</p>';
                }
            })
            .catch(error => {
                console.error('Erro no polling:', error);
            });
        }
        
        // Inicia o Polling periódico
        setInterval(fetchNewMessages, 2000); // Tenta buscar mensagens a cada 2 segundos
        
        // Carrega as mensagens iniciais
        fetchNewMessages(); 
    });
</script>
{{-- FIM DA ESTRUTURA CHAT --}}

    @stack('graficos')

<footer class="page-footer-custom">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            
            <div style="font-size: 0.8rem;">
                © {{ date('Y') }} <b>Grupo Cargo Polo</b> 
                <span style="margin: 0 8px; color: #ccc;">|</span> 
                Governança de Dados
            </div>

            <div>
                <a href="https://suporte.grupocargopolo.com.br:13004/WOListView.do" target="_blank" class="support-link-slim">
                    <i class="material-icons">help_outline</i>
                    Suporte Técnico
                </a>
            </div>

        </div>
    </div>
</footer>





<script>
    function carregarNotificacoes() {
        $.ajax({
            url: "{{ route('notifications.fetch') }}", 
            method: "GET",
            dataType: "json",
            // Dentro do seu success do AJAX:
            success: function(response) {
                const badge = $('#notif-badge');
                
                // Agora o contador mostra Globais + Privadas que ainda não foram lidas pelo user
                if (response.unread_count > 0) {
                    badge.text(response.unread_count).show();
                } else {
                    badge.hide();
                }

                const lista = $('#notif-lista');
                lista.empty();

                if (response.notifications.length > 0) {
                    response.notifications.forEach(function(n) {
                        let icon = n.is_global ? 'public' : 'notifications';
                        let iconColor = n.is_global ? 'orange-text' : 'blue-text';
                        let label = n.is_global ? '<span class="new badge orange left" data-badge-caption="AVISO" style="margin-right:10px; float:none;"></span>' : '';
                        
                        lista.append(`
                            <li>
                                <a href="/notificacoes/ler/${n.id}" style="line-height: 1.4; padding: 15px; display: block;">
                                    <i class="material-icons left ${iconColor}">${icon}</i>
                                    <strong style="display:block; color: #333;">${label} ${n.titulo}</strong>
                                    <span style="font-size: 12px; color: #666;">${n.mensagem}</span>
                                </a>
                            </li>
                            <li class="divider"></li>
                        `);
                    });
                } else {
                    lista.append('<li><a href="#!" class="grey-text center" style="padding:20px;">Tudo limpo por aqui!</a></li>');
                }
            }
        });
    }

    $(document).ready(function() {
        // Inicializa o Menu Lateral
        $('.sidenav').sidenav();

        // Inicializa os Menus Expansíveis (Acordions) do Sideout
        $('.collapsible').collapsible();

        // Inicializa os Dropdowns (Notificações, etc)
        $('.dropdown-trigger').dropdown({
            coverTrigger: false,
            constrainWidth: false
        });

        carregarNotificacoes();
    });
    </script>



</body>
</html>
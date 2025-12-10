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
      background-color: #f4f4f4; /* Fundo cinza claro suave */
    }

    .red{
      background-color: #184693 !important;
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
    <li><a href="{{route('reserva.reservas')}}">Minhas reservas</a></li>
    @auth
      @if (auth()->user()->admin == 1)
        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiYmNkNDJhZDQtMjk3NS00MDk0LWFhYmYtMzFhNDFmZjI4ZDIwIiwidCI6IjE5MDE3MzlkLTg1M2YtNDkwMS1iMTYwLTYxMDY4NWMwYzc5ZSJ9">Power BI</a></li>
        <li><a href="{{route('admin.dashboard')}}">Dashboard Viagens</a></li>
        <li><a href="{{route('admin.canceladas')}}">Viagens Canceladas</a></li>
        <li><a href="{{route('admin.finalizadas')}}">Viagens Finalizadas</a></li>
      @endif 
      @if (auth()->user()->admin == 3)
        <li><a href="{{route('fiscal.aprovacao')}}">Pedidos Fiscais</a></li>
        
        <li>
          <form action="{{ route('exportar.fiscais') }}" method="GET" style="margin: 0; padding: 0;">
            <button type="submit" class="btn green" style="width: 100%; text-align: left;">Exportar Dados</button>
          </form>
        </li>
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

      @endif

    @endauth
    <li><a href="{{route('reserva.sobre')}}">Sobre</a></li>
    <li><a href="{{route('login.logout')}}">Sair</a></li>
</ul>



  <nav class="red">
    <div class="nav-wrapper container">
      <a href="{{route('index')}}" class="brand-logo center">@yield('title')</a>
      <a href="#" class="brand-logo" href="index.html">
        <img src="{{ asset('img/LogoSite.png') }}" style="width: 100px; height: auto; margin:10px;margin-left:80px">
    </a>
      <ul id="nav-mobile" class="left">
        @auth
          <a href="#" data-target="slide-out" class="sidenav-trigger left  show-on-large"><i class="material-icons">menu</i></a>
        @else
            @php
                session()->flash('erro', 'Você precisa estar autenticado para acessar esta área.');
            @endphp

          <a href="{{route('login.form')}}" data-target="slide-out" class="sidenav-trigger left  show-on-large"><i class="material-icons">menu</i></a>
        @endauth
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
                    <img src="{{asset('img/office2.jpg')}}" style="opacity: 0.5"> 
                </div>
                <a href="#user"><img class="circle" src="https://upload.wikimedia.org/wikipedia/commons/a/a6/Anonymous_emblem.svg"></a>
                <a href="#name"><span class="white-text name"> {{auth()->user()->name }} </span></a>
                <a href="#email"><span class="white-text email"> {{auth()->user()->email}} </span></a>
                <a href="#filial"><span class="white-text filial"> {{auth()->user()->filial}} </span></a>
            </div>
        </li> 
        <li><a href="{{route('index')}}"><i class="material-icons">home</i>Home</a></li>
          <!-- Portal Reservas -->

          <li>
    <ul class="collapsible collapsible-accordion">
      <li>
        <a class="collapsible-header">
          <i class="material-icons">business_center</i>Portal Reservas
          <i class="material-icons right">arrow_drop_down</i>
            </a>
            <div class="collapsible-body">
              <ul>

              <li><a href="{{route('reserva.home')}}"><i class="material-icons">home</i>Home</a></li>
              <li><a href="{{route('reserva.reservas')}}"><i class="material-icons">description</i>Minhas Reservas</a></li>
              <li><a href="{{route('reserva.passagem-aerea')}}"><i class="material-icons">flight</i>Reservar Passagem</a></li>
              <li><a href="{{route('reserva.veiculo')}}"><i class="material-icons">directions_car</i>Reservar Veiculo</a></li>
              <li><a href="{{route('reserva.hospedagem')}}"><i class="material-icons">hotel</i>Reservar Hospedagem</a></li>
              <li><a href="{{route('reserva.adiantamento')}}"><i class="material-icons">attach_money</i>Solicitar Adiantamento</a></li>
              <li><a href="{{route('reserva.sobre')}}"><i class="material-icons">help</i>Sobre</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </li>
        {{-- <li><a href="{{route()}}"></a></li> --}}
        <li><a href="{{route('login.logout')}}"><i class="material-icons">exit_to_app</i>Sair</a></li>

  </ul>


    @endif

  @auth
  <div id="mini-chat-container" class="chat-closed">
    <div id="chat-header">
        Chat - Grupo Cargo Polo
        <button id="toggle-chat-button" aria-label="Abrir Chat">
            <span class="chat-toggle-icon">◀</span>
        </button>
    </div>
    <div id="chat-body">
        <div class="chat-messages">
             <p class="system-message">Carregando mensagens...</p>
        </div>
        <div class="chat-input">
            <input type="text" placeholder="Digite sua mensagem...">
            <button>Enviar</button>
        </div>
    </div>
</div>
@endauth


    </div>
  </nav>

@yield('conteudo')
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

</body>
</html>
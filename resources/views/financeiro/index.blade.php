@extends('layout')
@section('title', 'Central Financeiro')
@section('conteudo')



<div class="col s12 m6 offset-m3">
  <div class="container my-5">

    @if ($message = Session::get('success'))
    <div class="card green darken-1">
        <div class="card-content white-text">
        <span class="card-title">Sucesso!</span>
        <p>Parabéns! A solicitação foi realizada com sucesso!<br>
        </p>
        </div>
    </div>
    @endif

    {{-- for de 1 a 5 --}}
    @for ($i = 1; $i <= 5; $i++)
        @if ($message = Session::get('success'.$i))
            <div class="card green darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Sucesso!</span>
                    <p>Parabéns! A solicitação foi realizada com sucesso!<br>
                    </p>
                </div>
            </div>
        @endif
    @endfor

    {{-- Adicione isso logo acima do @if ($errors->any()) --}}
@if ($message = Session::get('error'))
    <div class="card red darken-1">
        <div class="card-content white-text">
            <span class="card-title">Atenção</span>
            <p>{{ $message }}</p>
        </div>
    </div>
@endif


  @if ($errors->any())
  <div class="alert alert-danger">
      <ul>
          @foreach ($errors->all() as $error)
          <div class="card red darken-1">
            <div class="card-content white-text">
              <span class="card-title">Erro</span>
              <p>Corrija os seguintes erros para prosseguir:<br>
                {{$error}}
             </p>
            </div>
          </div>
        @endforeach
      </ul>
    </div>
  </div>
@endif

</div>
</div>

<div class="container" style="padding-top: 50px; width: 95%;">
    <div class="dashboard-header-zone center-align">
        <h3 class="brand-title">Portal Financeiro <span class="accent-text">CargoPolo</span></h3>
        <p class="brand-tagline">PLATAFORMA INTEGRADA DE PAGAMENTOS E BI</p>
    </div>

    <div class="balanced-grid">
        {{-- CARD 1: PAINEL DE CONTROLE (GESTÃO E BI) --}}
        <a href="{{ route('financeiro.resumo') }}" class="premium-card master-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="live-indicator">
                    <span class="dot"></span> MÓDULO GESTOR
                </div>
            </div>
            
            <div class="card-info">
                <h4 class="item-title">Painel de Controle</h4>
                <p class="item-desc">Aprovação de chamados, liquidação de pagamentos, conferência de comprovantes e indicadores de BI.</p>
            </div>

            <div class="card-action">
                <span>Gerenciar Operações</span>
                <i class="fa-solid fa-arrow-right-long"></i>
            </div>
        </a>

        {{-- CARD 2: FORMULÁRIO DE SOLICITAÇÃO --}}
        <a href="{{ route('financeiro_fr.index') }}" class="premium-card request-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon request-icon">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div class="status-badge">ACESSO RÁPIDO</div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Nova Solicitação</h4>
                <p class="item-desc">Acesse o formulário para registrar novas demandas de pagamento, anexar notas e solicitar reembolsos.</p>
            </div>
            <div class="card-action">
                <span>Abrir Formulário</span>
                <i class="fa-solid fa-plus"></i>
            </div>
        </a>
    </div>
</div>



{{--
            <span class="card-title center"><b>Utilizar a nova versão 2.0 do <a href="{{route('financeiro_fr.index')}}">Formulário Financeiro</a>.</b></span>
            <span class="card-title center"><b>Instrução de Trabalho:</b> <a href="/instrucao_financeiro.pdf">Como preencher o formulário financeiro a vista corretamente?</a></span>
                   <span class="card-title center"><b>Caso tenha duvidas de preenchimento, favor abrir <a href="https://suporte.grupocargopolo.com.br:13004/WOListView.do">chamado</a> para T.I</b></span> --}}




      {{-- <span class="card-title center"><b>Selecione o tipo de Solicitação:</b></span> --}}

{{-- <form id="form-financeiro" action="{{route('financeiro.store')}}"method="POST" enctype="multipart/form-data" onsubmit="return validarEmails() && validarFormulario() && disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf
    <div class="btn-group center" role="group" aria-label="Tipo de Reserva">
        <input type="hidden" name="tipo" id="tipo" required>
        <button type="button" class="btn" data-value="avista">Pagamento à vista/Socorro em Rota</button>
        <button type="button" class="btn" data-value="reembolso">Reembolso/Despesa</button>
        <button type="button" class="btn" data-value="adiantamento">Adiantamento à Fornecedor (Almox)</button>
    </div><br>
    <br>

    <div id="campos-avista" class="tipo-campos" style="display:none;">

        <input type="number" name="pedido" id = "pedido_1" placeholder="Número Pedido de Compra" required>
        <input type="text" name="placa" id="placa" placeholder="Placa">

        <input type="text" name="referencia" placeholder="Descrição de Solicitação"><br><br>

        <span class="card-title center"><b>Dados do fornecedor</b></span>


        <input type="text" name="cnpj" placeholder="CNPJ/CPF">
        <input type="text" name="name" placeholder="Nome do Fornecedor">
        <input type="text" name="pamcard" placeholder="Pamcard"><br><br>


        <span class="card-title center"><b>Dados bancários</b></span>

        <input type="text" name="banco" placeholder="Banco">
        <input type="number" name="agencia" placeholder="Agencia">
        <input type="number" name="conta" placeholder="Conta">


        <input type="text" name="favorecido" placeholder="Nome do Favorecido">
        <input type="text" name="valor" placeholder="Valor">
        <input type="text" name="pix" placeholder="Chave Pix">

        Tipo de Chave Pix: <br>
        <select name="tipo_pix" id="tipo_pix" required>
    
            <option value=" "></option>
            <option value="Celular">Celular</option>
            <option value="CPF/CNPJ">CPF/CNPJ</option>
            <option value="E-mail">E-mail</option>
            <option value="Chave Aleatória">Chave Aleatória</option>
            <option value="Pix copia e cola">Pix copia e cola</option>
    
        </select>


        <br><br>
        Nota Fiscal/Recibo:<br>
        <input type="file" name="foto" id="foto" accept="image/*"><br><br>

        <input type="text" name="prazo" placeholder="Observações">

    </div>

    <div id="campos-adiantamento" class="tipo-campos" style="display:none;">

        <input type="number" name="pedido" id="pedido_2" placeholder="Número Pedido de Compra" required>

        <input type="text" name="placa" id="placa" placeholder="Placa">

        <input type="text" name="referencia" placeholder="Descrição de Solicitação"><br><br>

        <span class="card-title center"><b>Dados do fornecedor</b></span>


        <input type="text" name="cnpj" placeholder="CNPJ/CPF">
        <input type="text" name="name" placeholder="Nome do Fornecedor"><br><br>


        <span class="card-title center"><b>Dados bancários</b></span>

        <input type="text" name="banco" placeholder="Banco">
        <input type="number" name="agencia" placeholder="Agencia">
        <input type="number" name="conta" placeholder="Conta">
        <input type="text" name="favorecido" placeholder="Nome do Favorecido">
        <input type="text" name="valor" placeholder="Valor">
        <input type="text" name="pix" placeholder="Chave Pix">

        Tipo de Chave Pix: <br>
        <select name="tipo_pix" id="tipo_pix" required>
    
            <option value=" "></option>
            <option value="Celular">Celular</option>
            <option value="CPF/CNPJ">CPF/CNPJ</option>
            <option value="E-mail">E-mail</option>
            <option value="Chave Aleatória">Chave Aleatória</option>
            <option value="Pix copia e cola">Pix copia e cola</option>
    
        </select>


        <br><br>
        Nota Fiscal/Recibo:<br>
        <input type="file" name="foto" id="foto" accept="image/*"><br><br>

        <input type="text" name="prazo" placeholder="Prazo de NF"><br><br>

    </div>

    <div id="campos-reembolso" class="tipo-campos" style="display:none;">
        <input type="text" name="motivo" id="descr_compra" placeholder="Finalidade da Compra/Descr. Item" required>

        <input type="text" name="placa" id="placa" placeholder="Placa">

        <span class="card-title center"><b>Dados do Recebedor</b></span>


        <input type="text" name="cnpj" id="cpfCnpj" placeholder="CNPJ/CPF" required>
        <input type="text" name="name" placeholder="Nome do Recebedor"><br><br>


        <span class="card-title center"><b>Dados bancários</b></span>

        <input type="text" name="banco" placeholder="Banco">
        <input type="number" name="agencia" placeholder="Agencia">
        <input type="number" name="conta" placeholder="Conta">
        <input type="text" name="favorecido" placeholder="Nome do Favorecido">
        <input type="text" name="valor" placeholder="Valor">
        <input type="text" name="pix" placeholder="Chave Pix">

        Tipo de Chave Pix: <br>
        <select name="tipo_pix" id="tipo_pix" required>
    
            <option value=" "></option>
            <option value="Celular">Celular</option>
            <option value="CPF/CNPJ">CPF/CNPJ</option>
            <option value="E-mail">E-mail</option>
            <option value="Chave Aleatória">Chave Aleatória</option>
            <option value="Pix copia e cola">Pix copia e cola</option>
    
        </select>

        <br><br>

        Tipo de Reembolso: <br>
        <select name="tipo_reembolso[]" id="tipo_reembolso" multiple size="8" required>
    
            <option value=" "></option>
            <option value="Mecanica">Mecânica</option>
            <option value="Borracharia">Borracharia</option>
            <option value="Refeicao">Refeição/Alimentação</option>
            <option value="Manutencao">Manutenção</option>
            <option value="Hospedagem">Hospedagem</option>
            <option value="Estacionamento">Estacionamento</option>
            <option value="Escritorio">Escritório</option>
            <option value="Outros">Outros</option>
        </select>
        <br><br>

        Comprovante:<br>
        <input type="file" name="foto" id="foto-reembolso" accept=".pdf,image/*" required><br><br>

        Nota Fiscal/Recibo:<br>
        <input type="file" name="nota_fiscal" id="nota_fiscal" accept=".pdf,image/*" required><br><br>

        <input type="text" name="prazo" placeholder="Observações">


    </div>
    
    Filial: <br>
    <select name="filial" id="filial" required>

        <option value=" "></option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach

    </select> <br>
    <input type="email" name="email" placeholder="Email Solicitante (obrigatório)" required>
    <input type="email" name="email_gestor" placeholder="Email do Gestor (obrigatório)" required>

    <input type="text" name="emails" placeholder="E-mails separadoso por ;" >




    <br><br>

    <a href="{{route('index')}}">
      <button type="button" class="btn-cadastrar left">Voltar</button></a>
    <!-- Outros campos aqui -->
    <button type="submit" class="btn-cadastrar right">Enviar</button><br><br>
  </form> --}}
</div>
</div>

</div>


<style>
    :root {
        --deep-blue: #0a192f;
        --electric-blue: #007bff;
        --neon-cyan: #00f2ff;
        --soft-gray: #f8f9fa;
    }

    .dashboard-header-zone { margin-bottom: 50px; }
    .brand-title { font-weight: 900; color: var(--deep-blue); font-size: 2.2rem; letter-spacing: -1px; }
    .brand-title .accent-text { color: var(--electric-blue); }
    .brand-tagline { font-size: 0.7rem; font-weight: 700; color: #86868b; letter-spacing: 4px; }

    /* Grid Simétrica */
    .balanced-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    /* Base Premium */
    .premium-card {
        background: #ffffff;
        border-radius: 30px;
        padding: 45px 40px;
        text-decoration: none !important;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 350px;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    }

    /* Cores dos Cards */
    .master-card { background: var(--deep-blue) !important; color: white; }
    .request-card { background: white !important; border: 1px solid rgba(0,0,0,0.08); }

    /* Ícones Modernos */
    .main-icon {
        width: 65px; height: 65px;
        background: linear-gradient(135deg, var(--electric-blue), var(--neon-cyan));
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; color: white;
        box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);
    }

    .request-icon {
        background: var(--soft-gray);
        color: var(--deep-blue);
        box-shadow: none;
        border: 1px solid rgba(0,0,0,0.05);
    }

    /* Brilho Animado (Shimmer) */
    .shimmer {
        position: absolute; top: 0; left: -100%; width: 60%; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
        transform: skewX(-20deg);
        animation: swipe 7s infinite linear;
    }

    @keyframes swipe {
        0% { left: -120%; }
        15% { left: 150%; }
        100% { left: 150%; }
    }

    /* Badges de Status */
    .live-indicator, .status-badge {
        position: absolute; top: 45px; right: 40px;
        font-size: 10px; font-weight: 800; padding: 7px 14px; border-radius: 50px;
    }
    .live-indicator { color: var(--neon-cyan); background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 8px; }
    .status-badge { color: #5f6368; background: #f1f3f4; }

    .dot { width: 7px; height: 7px; background: var(--neon-cyan); border-radius: 50%; box-shadow: 0 0 10px var(--neon-cyan); animation: blink 2s infinite; }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    /* Textos */
    .item-title { font-size: 1.8rem; font-weight: 800; margin: 30px 0 12px 0; letter-spacing: -0.5px; }
    .request-card .item-title { color: var(--deep-blue); }
    .item-desc { font-size: 1.05rem; line-height: 1.5; opacity: 0.8; font-weight: 400; }
    .request-card .item-desc { color: #4b5563; }

    /* Botão de Ação no Card */
    .card-action {
        margin-top: 35px; display: flex; align-items: center; gap: 12px;
        font-weight: 700; font-size: 1rem; color: var(--neon-cyan);
        transition: 0.3s ease;
    }
    .request-card .card-action { color: var(--electric-blue); }

    /* Hover State */
    .premium-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.12);
    }
    .master-card:hover { border-color: var(--neon-cyan); }
    .request-card:hover { border-color: var(--electric-blue); }
    .premium-card:hover .card-action { gap: 18px; }

    /* Mobile */
    @media (max-width: 850px) {
        .balanced-grid { grid-template-columns: 1fr; }
        .premium-card { min-height: auto; padding: 35px; }
    }
</style>



<script>
  document.querySelectorAll('.btn-group .btn').forEach(button => {
      button.addEventListener('click', function() {
          // Remove a classe active de todos os botões
          document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
          
          // Adiciona a classe active ao botão clicado
          this.classList.add('active');
          
          // Atualiza o valor do campo hidden
          document.getElementById('tipo').value = this.getAttribute('data-value');
      });
  });
  </script>


<script>
function validarFormulario() {
  const tipo = document.getElementById('tipo').value;

  // 1️⃣  Primeiro, desativa o required de todos os blocos escondidos
  document.querySelectorAll('.tipo-campos').forEach(div => {
    if (div.style.display === 'none') {
      div.querySelectorAll('[required]').forEach(el => {
        el.dataset.tmpRequired = "1";     // guarda info p/ restaurar se precisar
        el.removeAttribute('required');
      });
    }
  });

  // 2️⃣  Validação extra que você já tem (exemplo do campo foto)
  const foto = document.getElementById('foto');
  if (tipo === 'devolucao' && (!foto || foto.files.length === 0)) {
    alert('O campo "Nota Fiscal da operação de compra" é obrigatório para o tipo "' + tipo + '".');
    foto.focus();
    return false;
  }



  // 3️⃣  Se chegou aqui, deixa o navegador validar normalmente os visíveis
  return true;
}
</script>



    
  
<script>
    const buttons = document.querySelectorAll('.btn-group .btn');
    const tipoInput = document.getElementById('tipo');
  
    const camposAvista = document.getElementById('campos-avista');
    const camposReembolso = document.getElementById('campos-reembolso');
    const camposAdiantamento = document.getElementById('campos-adiantamento');
  
    function esconderTodosCampos() {
      document.querySelectorAll('.tipo-campos').forEach(div => {
        div.style.display = 'none';
  
        // Desabilita todos inputs, selects e textareas dentro da div
        div.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
      });
    }
  
    function habilitarCampos(div) {
      div.style.display = 'block';
  
      // Habilita inputs, selects e textareas da div visível
      div.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
    }
  
    buttons.forEach(button => {
      button.addEventListener('click', () => {
        const valor = button.getAttribute('data-value');
        tipoInput.value = valor;
  
        esconderTodosCampos();
  
        if (valor === 'avista') {
          habilitarCampos(camposAvista);
        } else if (valor === 'reembolso') {
          habilitarCampos(camposReembolso);
        } else if (valor === 'adiantamento') {
          habilitarCampos(camposAdiantamento);
        }
      });
    });
  
    // Opcional: ao carregar a página, esconder e desabilitar tudo
    window.addEventListener('load', () => {
      esconderTodosCampos();
    });
  </script>
  

  <script>
    function validarEmails() {
        const campo = document.getElementById('emails');
        const valor = campo.value.trim();
    
        if (valor === '') return true; // Campo vazio é permitido
    
        const emails = valor.split(';').map(email => email.trim());
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
        const emailsInvalidos = emails.filter(email => !regex.test(email));
    
        if (emailsInvalidos.length > 0) {
            alert('Os seguintes e-mails são inválidos:\n' + emailsInvalidos.join('\n'));
            return false;
        }
    
        return true; // Tudo certo, envia o formulário
    }
    </script>


<script>
    function fecharAlerta() {
        document.getElementById('alerta-nova-versao').style.display = 'none';
        // Opcional: Salvar no navegador para não mostrar de novo nesta sessão
        sessionStorage.setItem('aviso_versao_lido', 'true');
    }

    // Verifica se o usuário já fechou o aviso anteriormente nesta sessão
    window.onload = function() {
        if (sessionStorage.getItem('aviso_versao_lido') === 'true') {
            document.getElementById('alerta-nova-versao').style.display = 'none';
        }
    };
</script>


@endsection
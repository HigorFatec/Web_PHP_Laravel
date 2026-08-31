@extends('layout')
@section('title', 'Financeiro')
@section('conteudo')

@if (@auth()->user()->id != null)


<div class="row">
<div class="col s12 m6 offset-m3">

    @if ($message = Session::get('success'))
    <div class="card green darken-1">
        <div class="card-content white-text">
        <span class="card-title">Sucesso!</span>
        <p>Parabéns! A Transferência Pix foi solicitada com sucesso!<br>
        </p>
        </div>
    </div>
    @endif

    @if ($message = Session::get('aprovado'))
    <div class="card green darken-1">
        <div class="card-content white-text">
        <span class="card-title">Sucesso!</span>
        <p>Parabéns! A Solicitação de Transferência Pix foi aprovada com sucesso!<br>
        </p>
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
@endif

<div class="card">
  <div class="card-content">
      <span class="card-title center"><b>Financeiro</b></span><br>
      <span class="card-title center"><b>Selecione o tipo de Solicitação:</b></span>

<form id="form-financeiro" action="{{route('financeiro_fr.store')}}"method="POST" enctype="multipart/form-data" onsubmit="return validarEmails() && validarFormulario() && disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf
        <div class="btn-group center" role="group" aria-label="Tipo de Reserva">
            <input type="hidden" name="tipo" id="tipo" value="">

            <button type="button" class="btn" data-value="avista">Pagamento à vista/Socorro em Rota</button>

            <a href="{{ route('reembolso.create') }}" class="btn">Reembolso/Despesa</a>
        </div><br>





    <div id="campos-avista" class="tipo-campos" style="display:none;">

        <input type="text" name="solicitante" placeholder="Nome do Solicitante" required>

        Adiantamento à Fornecedor ? <b>(Almox)</b><br>
        <select name="adiantamento_fornecedor" id="adiantamento_fornecedor" required>
            <option value="nao">Não</option>
            <option value="sim">Sim</option>
        </select>

        Socorro em Rota? <br>
        <select name="socorro_em_rota" id="socorro_em_rota" required>
            <option value="nao">Não</option>
            <option value="sim">Sim</option>
        </select>

        Fornecedor Emite Nota Fiscal ? <b>(se responder errado, terá que refazer)</b> <br>
        <select name="tem_nota_fiscal" id="tem_nota_fiscal" required>
           <option value="nao">Não</option> {{-- BAN RAZ --}}
           <option value="sim">Sim</option>
        </select>

        <input type="text" name="pedido" id = "pedido_1" placeholder="Número Pedido de Compra(rodopar)" maxlength="6">
        <input type="text" name="placa" id="placa" placeholder="Placa" maxlength="10">


        Frota Bloqueada ? <b><i>Obs:(apenas para socorro em rota)</i></b> <br>
        <select name="frota_bloqueada" id="frota_bloqueada" required>
            <option value="nao">Não</option>
            <option value="sim">Sim</option>
        </select>

        <input type="text" name="referencia" placeholder="Descrição de Solicitação"><br><br>

        <span class="card-title center"><b>Dados do fornecedor</b></span>
        <center><span>Não encontrou o fornecedor? <a href="{{ route('fisico.fornecedor_financeiro') }}">Clique aqui para cadastrar</a></span></center>

        Fornecedor: <br>
        <input type="text" id="search_fornecedor" placeholder="Buscar fornecedor...">

        <select name="fornecedor" id="fornecedor_select" class="browser-default" required>
            <option value="">Selecione...</option>
        </select>


        <input type="text" id="cnpj_input" name="cnpj" placeholder="CNPJ/CPF" readonly>
        <input type="text" id="name_input" name="name" placeholder="Nome do Fornecedor">
        <input type="text" name="pamcard" placeholder="Pamcard"><br><br>


        <span class="card-title center"><b>Dados bancários</b></span>

        <input type="text" id="banco_input" name="banco" placeholder="Banco">
        <input type="number" id ="agencia_input" name="agencia" placeholder="Agencia">
        <input type="number" id="conta_input" name="conta" placeholder="Conta">

        Favorecido (quem irá receber):<br>
        <input type="text" id="favorecido_input" name="favorecido" placeholder="Nome do Favorecido"><br>
        <input type="text" id="valor" name="valor" placeholder="Valor">

        Pix: <i> (Obs <b>se estiver em branco, peça para cadastrarem no rodopar</b>)</i> <br>
        <input type="text" name="pix" placeholder="Chave Pix">

        Tipo de Chave Pix: <br>

        {{-- <div id="tipo_pix" data-valor="{{ $pix_tipo }}">
            @if($pix_tipo == 1)
                <span>Opção: CPF/CNPJ</span>
            @elseif($pix_tipo == 2)
                <span>Opção: E-mail</span>
            @endif
        </div> --}}

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
        <input type="text" name="favorecido" placeholder="Nome do Favorecido" required>
        <input type="text" name="valor" placeholder="Valor">
        <input type="text" id="pix_input" name="pix" placeholder="Chave Pix"><br>

        {{-- <label>Tipo de Chave Pix:</label>
        <input type="text" 
        name="tipo_pix" 
        id="tipo_pix_input" 
        class="form-control" 
        readonly 
        placeholder="Selecione um fornecedor..." 
        style="background-color: #f8f9fa; cursor: not-allowed;"> --}}
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
        <input type="text" name="name" placeholder="Nome do Recebedor">
        <input type="text" name="pamcard" placeholder="Pamcard"><br><br>


        <span class="card-title center"><b>Dados bancários</b></span>

        <input type="text" name="banco" placeholder="Banco">
        <input type="number" name="agencia" placeholder="Agencia">
        <input type="number" name="conta" placeholder="Conta"><br>

        Favorecido <b>(quem irá receber)</b>:<br>
        <input type="text" name="favorecido" placeholder="Nome do Favorecido" required>
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
    
    <br>
    Filial: <br>
    <select id="unidade_negocio_select" name="cod_unidade" class="browser-default" required>
        <option value="">Selecione a Unidade</option>
        @foreach ($unidade->unique('DESCRI') as $filial)
            <option value="{{ $filial->CODUNN }}">
                {{ $filial->DESCRI }}
            </option>
        @endforeach
    </select><br>

    Centro de Custo: <br>
    <select id="centro_custo_select" name="cod_custo" class="browser-default" required>
        <option value=""></option>
        @foreach ($custo->unique('DESCRI') as $filial)
            <option value="{{ $filial->CODCUS }}">
                {{ $filial->DESCRI }}
            </option>
        @endforeach
    </select><br>

    Centro de Gasto: <br>
    <select id="centro_gasto_select" name="cod_gasto" class="browser-default" required>
        <option value=""></option>
        @foreach ($gasto->unique('DESCRI') as $filial)
            <option value="{{ $filial->CODCGA }}">
                {{ $filial->DESCRI }}
            </option>
        @endforeach
    </select><br>

    Gestor Aprovador: <br>
    <i><span>Não encontrou o gestor? <a href="https://suporte.grupocargopolo.com.br:13004/WOListView.do">Clique aqui para abrir um chamado para cadastro</a></span></i>
    <select name="gestor_aprovador" id="gestor_aprovador" class="browser-default" required>
        <option value=""></option>
        @foreach ($filiais->groupBy('email_gestor') as $email => $itens)
            @php
                // Junta todos os códigos de unidade e centros de custo deste gestor separados por vírgula
                $unidades = $itens->pluck('cod_unidade')->map(fn($v) => trim($v))->unique()->implode(',');
                $custos = $itens->pluck('cod_custo')->map(fn($v) => trim($v))->unique()->implode(',');
                $nomeGestor = $itens->first()->nome_gestor;
            @endphp
            <option value="{{ $email }}" 
                    data-unidade="{{ $unidades }}" 
                    data-custo="{{ $custos }}">
                {{ $nomeGestor }}
            </option>
        @endforeach
    </select><br><br>






    <input type="email" name="email" placeholder="Email Solicitante (obrigatório)" required>
    <input type="email" name="email_gestor" placeholder="Email do Gestor (obrigatório)" required>

    <input type="text" name="emails" placeholder="E-mails separadoso por ;" >




    <br><br>

    <a href="{{route('index')}}">
      <button type="button" class="btn-cadastrar left">Voltar</button></a>
    <!-- Outros campos aqui -->
    <button type="submit" class="btn-cadastrar right">Enviar</button><br><br>
  </form>
</div>
</div>

</div>







<script>
$(function () {

    const $input = $('#search_fornecedor');
    const $select = $('#fornecedor_select');

    if ($input.length === 0 || $select.length === 0) {
        console.warn('Elemento #search_fornecedor ou #fornecedor_select não encontrado.');
        return;
    }

    // ---- Debounce ----
    function debounce(fn, delay) {
        let timer = null;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, arguments), delay);
        };
    }

    // ---- Função AJAX corrigida ----
    function buscar(termo) {
        if (termo.length < 2) return;

        $.ajax({
            url: '/fornecedores/buscar',
            method: 'GET',
            data: { search: termo },
            success: function (data) {

                console.log("[AJAX] fornecedores:", data);

                $select.empty();
                $select.append('<option value="">Selecione...</option>');

                if (!data || !Array.isArray(data) || data.length === 0) {
                    $select.append('<option value="">Nenhum fornecedor encontrado</option>');
                    return;
                }

                data.forEach(f => {
                    $select.append(`
                        <option value="${f.codclifor}"
                            data-cnpj="${f.cnpj ?? ''}"
                            data-razsoc="${f.razsoc ?? ''}"
                            data-banco="${f.banco ?? ''}"
                            data-agencia="${f.agencia ?? ''}"
                            data-conta="${f.conta ?? ''}"
                            data-favorecido="${f.favorecido ?? ''}"
                            data-pix="${f.pix ?? ''}"
                            data-tipo_pix="${f.tipo_pix ?? ''}">
                            ${f.codclifor} - ${f.razsoc} - ${f.cnpj}
                        </option>
                    `);
                });
            },
            error: function (xhr) {
                console.error("[AJAX] erro:", xhr.responseText);
            }
        });
    }

    // ---- Input Listener com Debounce ----
    $input.on('input', debounce(function () {
        buscar($(this).val().trim());
    }, 400));

});
</script>









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
    $(document).ready(function() {
        $('#fornecedor_select').select2({
            placeholder: 'Selecione ou pesquise o fornecedor',
            width: '100%'
        });
    });
    </script>


<script>
  // PRENCHIMENTO AUTOMATICO DE INPUTS
$(function() {
    // --- Inicializa Select2 apenas se não estiver aplicado ---
    if (typeof $.fn.select2 === 'function') {
        if (!$('#fornecedor_select').hasClass('select2-applied')) {
            $('#fornecedor_select').select2({
                placeholder: 'Selecione ou pesquise o fornecedor',
                width: '100%'
            }).addClass('select2-applied');
        }
    }

    // --- Função que atualiza o input de cnpj a partir da opção selecionada ---
    function atualizaCnpjDoSelect(el) {
        var $sel = $(el);
        // pega option selecionada (compatível com select2 e select normal)
        var $opt = $sel.find('option:selected');
        var cnpj = $opt.data('cnpj') || '';
        $('#cnpj_input').val(cnpj);
        var name = $opt.data('razsoc') || '';
        $('#name_input').val(name);
        var banco = $opt.data('banco') || '';
        $('#banco_input').val(banco);
        var agencia = $opt.data('agencia') || '';
        $('#agencia_input').val(agencia);
        var conta = $opt.data('conta') || '';
        $('#conta_input').val(conta);
        var favorecido = $opt.data('favorecido') || '';
        $('#favorecido_input').val(favorecido);
        var pix = $opt.data('pix') || '';
        $('#pix_input').val(pix);
        var tipo_pix = $opt.data('tipo_pix') || '';
        $('#tipo_pix_input').val(tipo_pix);
    
    }

    // --- escuta mudança no select (pega tanto evento change nativo quanto os do Select2) ---
    $('#fornecedor_select').on('change', function() {
        atualizaCnpjDoSelect(this);
    });

    // Select2 também dispara 'select2:select' — garantimos captura
    $('#fornecedor_select').on('select2:select', function(e) {
        atualizaCnpjDoSelect(this);
    });

    // --- DEBUG: se ainda não preencher, rode estes comandos no console do navegador ---
    // console.log($('#fornecedor_select').val());
    // console.log($('#fornecedor_select option:selected').data('cnpj'));

    // opcional: popula ao carregar caso queira preservar um valor antigo
    atualizaCnpjDoSelect($('#fornecedor_select'));
});
</script>


<script>
    const inputValor = document.getElementById('valor');

    // Permite apenas números, vírgula e ponto
    inputValor.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9.,]/g, "");
    });

    // Converte para float ao sair do campo
    inputValor.addEventListener('blur', function () {
        let v = this.value.replace(",", "."); // troca vírgula por ponto
        let floatVal = parseFloat(v);

        if (!isNaN(floatVal)) {
            this.value = floatVal.toFixed(2); // formata com duas casas decimais (opcional)
        } else {
            this.value = ""; // se não for número válido, limpa
        }
    });
</script>
















<script>
document.getElementById('pedido_1').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>



@else
<script>
    window.location.href = '/login';
</script>
@endif


<script>
$(document).ready(function() {
    $('#pix_input').on('blur', function() {
        let valor = $(this).val().trim();

        // 1. Se for E-mail: Remove apenas espaços
        if (valor.includes('@')) {
            valor = valor.replace(/\s/g, '');
        } 
        // 2. Se for Telefone, CPF ou CNPJ: Mantém apenas números
        // O Mercado Pago prefere receber apenas números nesses casos
        else {
            // Remove pontos, traços, parênteses, espaços e o símbolo +
            valor = valor.replace(/\D/g, ''); 
            
            // Se for telefone (ex: 16991234567), você pode opcionalmente 
            // garantir que não tenha o +55 aqui, ou deixar para o Controller
        }

        $(this).val(valor);
    });
});
</script>



<script>
$(document).ready(function() {
function atualizarFiltroGestor() {
    let unidadeSelecionada = $.trim($('#unidade_negocio_select').val());
    let custoSelecionado = $.trim($('#centro_custo_select').val());
    let $gestorSelect = $('#gestor_aprovador');
    let $options = $gestorSelect.find('option');
    
    console.log("[DEBUG] Unidade da Tela:", unidadeSelecionada);
    console.log("[DEBUG] Centro de Custo da Tela:", custoSelecionado);

    if (!unidadeSelecionada) {
        $options.each(function() {
            if ($(this).val() !== "") $(this).hide().prop('disabled', true);
        });
        $gestorSelect.val("");
        return;
    }

    let totalVisiveis = 0;

    $options.each(function() {
        let $opt = $(this);
        if ($opt.val() === "") return;

        // Transforma as listas de strings do HTML em Arrays do JS
        let unidadesDoGestor = ($opt.attr('data-unidade') || '').split(',');
        let custosDoGestor = ($opt.attr('data-custo') || '').split(',');

        // ---- REGRA ESPECIAL: SE FOR A FILIAL 19 ----
        if (unidadeSelecionada === "19") {
            // O gestor precisa ter a unidade 19 E o centro de custo selecionado associados a ele
            if (unidadesDoGestor.includes("19") && custosDoGestor.includes(custoSelecionado)) {
                $opt.show().prop('disabled', false);
                totalVisiveis++;
            } else {
                $opt.hide().prop('disabled', true);
            }
        } 
        // ---- REGRA PADRÃO: OUTRAS FILIAIS (Ex: 70) ----
        else {
            // Ignora o centro de custo. Só valida se a unidade da tela está na lista do gestor
            if (unidadesDoGestor.includes(unidadeSelecionada)) {
                $opt.show().prop('disabled', false);
                totalVisiveis++;
            } else {
                $opt.hide().prop('disabled', true);
            }
        }
    });

    console.log("[DEBUG] Total de gestores encontrados:", totalVisiveis);
    $gestorSelect.val("");

    if (typeof $.fn.formSelect === 'function') {
        $gestorSelect.formSelect();
    }
}

    // Eventos de mudança
    $('#unidade_negocio_select').on('change', function() {
        atualizarFiltroGestor();
    });

    $('#centro_custo_select').on('change', function() {
        if ($('#unidade_negocio_select').val() == "19") {
            atualizarFiltroGestor();
        }
    });

    // Evento ao sair do campo Pedido (Busca automática do Rodopar)
    $('#pedido_1').on('blur', function() {
        const numPed = $(this).val();
        const tipo = $('#tipo').val();
        const temNota = $('#tem_nota_fiscal').val();

        if (numPed.length > 0 && tipo === 'avista' && temNota === 'sim') {
            $.ajax({
                url: '/financeiro/buscar-pedido/' + numPed,
                method: 'GET',
                success: function(data) {
                    $('#unidade_negocio_select').val(data.CODUNN).addClass('select-travado');
                    $('#centro_custo_select').val(data.CODCUS).addClass('select-travado');
                    $('#centro_gasto_select').val(data.CODCGA).addClass('select-travado');

                    atualizarFiltroGestor();
                    
                    if (data.CODUNN != "19") {
                        let primeiroGestor = $('#gestor_aprovador option:not(:disabled)[value!=""]').first().val();
                        if (primeiroGestor) {
                            $('#gestor_aprovador').val(primeiroGestor);
                            if (typeof $.fn.formSelect === 'function') $('#gestor_aprovador').formSelect();
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Pedido não encontrado no Rodopar.');
                    $('.browser-default').removeClass('select-travado');
                }
            });
        }
    });

    // Reset ao mudar "Tem Nota Fiscal"
    $('#tem_nota_fiscal').on('change', function() {
        const temNota = $(this).val();
        const selects = $('#unidade_negocio_select, #centro_custo_select, #centro_gasto_select, #gestor_aprovador');
        
        if (temNota === 'nao') {
            $('#pedido_1').val('');
            selects.removeClass('select-travado').val('');
            $('#gestor_aprovador option').show().prop('disabled', false);
            if (typeof $.fn.formSelect === 'function') $('#gestor_aprovador').formSelect();
        } else {
            $('#pedido_1').val('').focus();
            selects.val('');
            alert('Para prosseguir, informe o número do pedido.');
        }
    });
});
</script>


<style>
    .select-travado {
        pointer-events: none;
        background-color: #f5f5f5 !important;
        color: #9e9e9e !important;
        cursor: not-allowed;
    }
</style>

@endsection
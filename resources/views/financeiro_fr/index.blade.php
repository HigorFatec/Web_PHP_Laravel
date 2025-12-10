@extends('layout')
@section('title', 'Financeiro')
@section('conteudo')



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
        <input type="hidden" name="tipo" id="tipo" required>
        <button type="button" class="btn" data-value="avista">Pagamento à vista/Socorro em Rota</button>
        <button type="button" class="btn" data-value="reembolso">Reembolso/Despesa</button>
        <button type="button" class="btn" data-value="adiantamento">Adiantamento à Fornecedor (Almox)</button>
    </div><br>
    <br>





    <div id="campos-avista" class="tipo-campos" style="display:none;">

        <input type="text" name="solicitante" placeholder="Nome do Solicitante" required>

        Socorro em Rota? <br>
        <select name="socorro_em_rota" id="socorro_em_rota" required>
            <option value="nao">Não</option>
            <option value="sim">Sim</option>
        </select>

        Tem Nota Fiscal? <br>
        <select name="tem_nota_fiscal" id="tem_nota_fiscal" required>
           <option value="nao">Não</option> {{-- BAN RAZ --}}
           <option value="sim">Sim</option>
        </select>

        <input type="text" name="pedido" id = "pedido_1" placeholder="Número Pedido de Compra(rodopar)" maxlength="6">
        <input type="text" name="placa" id="placa" placeholder="Placa">

        <input type="text" name="referencia" placeholder="Descrição de Solicitação"><br><br>

        <span class="card-title center"><b>Dados do fornecedor</b></span>
        <center><span>Não encontrou o fornecedor? <a href="{{ route('fisico.fornecedor_financeiro') }}">Clique aqui para cadastrar</a></span></center>

        Fornecedor: <br>
        <select name="fornecedor" id="fornecedor_select" class="browser-default" required>
            <option value=""></option>
            @foreach ($fornecedores as $fornecedor)
                <option value="{{ $fornecedor->codclifor }}" data-cnpj="{{ $fornecedor->cnpj }}" data-name="{{ $fornecedor->razsoc }}" data-banco="{{ $fornecedor->banco }}" data-agencia="{{ $fornecedor->agencia }}" data-conta="{{ $fornecedor->conta }}" data-favorecido="{{ $fornecedor->favorecido }}" data-pix_aleatorio="{{$fornecedor->pix_aleatorio }}">
                    {{ $fornecedor->codclifor }} {{ $fornecedor->razsoc }}
                </option>
            @endforeach
        </select><br><br>


        <input type="text" id="cnpj_input" name="cnpj" placeholder="CNPJ/CPF">
        <input type="text" id="name_input" name="name" placeholder="Nome do Fornecedor">
        <input type="text" name="pamcard" placeholder="Pamcard"><br><br>


        <span class="card-title center"><b>Dados bancários</b></span>

        <input type="text" id="banco_input" name="banco" placeholder="Banco">
        <input type="number" id ="agencia_input" name="agencia" placeholder="Agencia">
        <input type="number" id="conta_input" name="conta" placeholder="Conta">


        <input type="text" id="favorecido_input" name="favorecido" placeholder="Nome do Favorecido">
        <input type="text" id="valor" name="valor" placeholder="Valor">
        <input type="text" id="pix_input" name="pix" placeholder="Chave Pix">

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
        <input type="text" name="name" placeholder="Nome do Recebedor">
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
    <select id="filial_select" name="cod_unidade" class="browser-default" required>
        <option value=""></option>
        @foreach ($filiais->unique('cod_unidade') as $filial)
            <option value="{{ $filial->cod_unidade }}">
                {{ $filial->unidade_negocio }}
            </option>
        @endforeach
    </select><br>

    Centro de Custo: <br>
    <select id="centro_custo_select" name="cod_custo" class="browser-default" required>
        <option value=""></option>
        @foreach ($filiais->unique(fn($item) => $item->cod_custo . '-' . $item->cod_unidade) as $filial)
            <option value="{{ $filial->cod_custo }}" data-unidade="{{ $filial->cod_unidade }}">
                {{ $filial->descri_custo }}
            </option>
        @endforeach
    </select><br>

    Centro de Gasto: <br>
    <select id="centro_gasto_select" name="cod_gasto" class="browser-default" required>
        <option value=""></option>
        @foreach ($filiais->unique(fn($item) => $item->cod_gasto . '-' . $item->cod_unidade) as $filial)
            <option value="{{ $filial->cod_gasto }}" data-unidade="{{ $filial->cod_unidade }}">
                {{ $filial->descri_gasto }}
            </option>
        @endforeach
    </select><br>

    Gestor Aprovador: <br>
    <select name="gestor_aprovador" id="gestor_aprovador" class="browser-default" required>
        <option value=""></option>
        @foreach ($filiais->unique(fn($item) => $item->cod_unidade . '-' . $item->cod_custo) as $filial)
            <option value="{{ $filial->email_gestor }}" data-unidade="{{ $filial->cod_unidade }}">
                {{ $filial->nome_gestor }}
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
        var name = $opt.data('name') || '';
        $('#name_input').val(name);
        var banco = $opt.data('banco') || '';
        $('#banco_input').val(banco);
        var agencia = $opt.data('agencia') || '';
        $('#agencia_input').val(agencia);
        var conta = $opt.data('conta') || '';
        $('#conta_input').val(conta);
        var favorecido = $opt.data('favorecido') || '';
        $('#favorecido_input').val(favorecido);
        var pix_aleatorio = $opt.data('pix_aleatorio') || '';
        $('#pix_input').val(pix_aleatorio);
    
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
  // PRENCHIMENTO AUTOMATICO DE INPUTS

function atualizaCustoDoSelect(el) {
    let opt = $(el).find('option:selected');

    let codCusto = opt.data('cod_custo') || '';
    $('#cod_custo_input').val(codCusto);

    let descriCusto = opt.data('descri_custo') || '';
    $('#descri_custo_input').val(descriCusto);
}

</script>




<script>
document.getElementById('filial_select').addEventListener('change', function () {
    let unidade = this.value;

    // Centro de Custo
    document.querySelectorAll('#centro_custo_select option').forEach(opt => {
        opt.hidden = opt.getAttribute('data-unidade') !== unidade && opt.value !== "";
    });

    // Centro de Gasto
    document.querySelectorAll('#centro_gasto_select option').forEach(opt => {
        opt.hidden = opt.getAttribute('data-unidade') !== unidade && opt.value !== "";
    });

    // Gestor Aprovador
    document.querySelectorAll('#gestor_aprovador option').forEach(opt => {
        opt.hidden = opt.getAttribute('data-unidade') !== unidade && opt.value !== "";
    });

    // limpa seleção anterior
    document.getElementById('centro_custo_select').value = "";
    document.getElementById('centro_gasto_select').value = "";
    document.getElementById('gestor_aprovador').value = "";
});
</script>



<script>
document.getElementById('pedido_1').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>







@endsection
@extends('financeiro.layout')
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

<form id="form-financeiro" action="{{route('financeiro.store')}}"method="POST" enctype="multipart/form-data" onsubmit="return validarEmails() && validarFormulario() && disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf
    <div class="btn-group center" role="group" aria-label="Tipo de Reserva">
        <input type="hidden" name="tipo" id="tipo" required>
        <button type="button" class="btn" data-value="avista">Pagamento à vista/Socorro em Rota</button>
        <button type="button" class="btn" data-value="reembolso">Reembolso/Despesa</button>
        <button type="button" class="btn" data-value="adiantamento">Adiantamento à Fornecedor (Almox)</button>
    </div><br>
    <br>

    <div id="campos-avista" class="tipo-campos" style="display:none;">

        <input type="number" name="pedido" id = "pedido_1" placeholder="Número Pedido de Compra" >
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

        <input type="number" name="pedido" id="pedido_2" placeholder="Número Pedido de Compra">

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
        <input type="text" name="motivo" id="descr_compra" placeholder="Finalidade da Compra/Descr. Item">

        <input type="text" name="placa" id="placa" placeholder="Placa">

        <span class="card-title center"><b>Dados do Recebedor</b></span>


        <input type="text" name="cnpj" id="cpfCnpj" placeholder="CNPJ/CPF">
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
        <input type="file" name="foto" id="foto-reembolso" accept=".pdf,image/*"><br><br>

        Nota Fiscal/Recibo:<br>
        <input type="file" name="nota_fiscal" id="nota_fiscal" accept=".pdf,image/*"><br><br>

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
      const foto = document.getElementById('foto-reembolso');
      const pedido2 = document.getElementById('pedido_2');
      const descr_compra = document.getElementById('descr_compra');
      const cpfCnpj = document.getElementById('cpfCnpj');
      const nota_fiscal = document.getElementById('nota_fiscal');
    
      // Se for "avista" ou "reembolso", o campo foto deve estar preenchido
      if ((tipo === 'reembolso') && (!foto || foto.files.length === 0) && (!nota_fiscal || nota_fiscal.files.length === 0)) {
        alert('Os anexos "Comprovante" e "Nota Fiscal" é obrigatório para o tipo "' + tipo + '".');
        foto.focus();
        return false; // impede o envio
      }

      if ((tipo === 'reembolso') && (descr_compra.value === '')){
        alert('O campo "Finalidade da Compra/Descr. Item" é obrigatório para o tipo "' + tipo + '".');
        descr_compra.focus();
        return false; // impede o envio
      }

      if((tipo === 'reembolso') && (cpfCnpj.value === '')){
        alert('O campo "CNPJ/CPF" é obrigatório para o tipo "' + tipo + '".');
        cpfCnpj.focus();
        return false; // impede o envio
      }


      if ((tipo === 'adiantamento') && (pedido2.value === '')){
        alert('O campo "Pedido" é obrigatório para o tipo "' + tipo + '".');
        pedido.focus();
        return false; // impede o envio
      }

    
      return true; // permite o envio
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


@endsection
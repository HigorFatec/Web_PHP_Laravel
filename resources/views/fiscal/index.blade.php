@extends('layout')
@section('title', 'Nota Fiscal - Emissão')
@section('conteudo')


<div class="row">
<div class="col s12 m6 offset-m3">

    @if ($message = Session::get('success'))
    <div class="card green darken-1">
        <div class="card-content white-text">
        <span class="card-title">Sucesso!</span>
        <p>Parabéns! A solicitação foi realizada com sucesso!<br>
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
      <span class="card-title center"><b>Emissão de NF</b></span><br>
      <span class="card-title center"><b>Selecione o tipo de Solicitação:</b></span>

<form id="form-fiscal" action="{{route('fiscal.store')}}"method="POST" enctype="multipart/form-data" onsubmit="return validarEmails() && validarFormulario() && disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf
    <div class="btn-group center" role="group" aria-label="Tipo de Reserva">
        <input type="hidden" name="tipo" id="tipo" required>
        <button type="button" class="btn" data-value="devolucao">Devolução</button>
        <button type="button" class="btn" data-value="venda">Venda</button>
        <button type="button" class="btn" data-value="remessa">Remessa</button>
        <button type="button" class="btn" data-value="descarte">Descarte</button>

    </div><br>
    <br>

  <b><span class="card-title center"><b>Nota Fiscal será emitida apenas após a aprovação do Gestor responsável</b></span></b><br>


        Filial: <br>
    <select name="filial" id="filial" required>

        <option value=" "></option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach

    </select> <br>

    <div id="campos-devolucao" class="tipo-campos" style="display:none;">


        <span class="card-title center"><b>Dados da Solicitação</b></span>

        <input type="text" name="empresa_solicitante" placeholder="Empresa solicitante" required>
        <input type="text" name="cnpj" placeholder="CNPJ" required>

        <input type="text" name="fornecedor" placeholder="Fornecedor" required>

        <input type="text" name="cnpj_fornecedor" placeholder="CNPJ do Fornecedor" required>
        <input type="text" name="nota_fiscal" placeholder="Nota Fiscal de origem" required>
        <input type="text" name="valor_devolucao" placeholder="Valor Total da devolução" required>
        <input type="text" name="valor_nf" placeholder="Valor Total da Nota Fiscal de Compra" required>
        <input type="text" name="mercadoria_devolvida" placeholder="Mercadoria a ser devolvida" required>
        <input type="text" name="motivo_operacao" placeholder="Motivo da Devolução" required><br>

        Finalidade da compra: <br>
        <select name="finalidade_da_compra" required>
            <option value=" "></option>
            <option value="uso_consumo">Uso e Consumo</option>
            <option value="ativo_imobilizado">Ativo imobilizado</option>
            <option value="estoque">Estoque</option>
            <option value="peca">Peças para aplicação direta</option>
        </select>

                <span class="card-title center"><b>Itens</b></span>

        <div class="produtos">
            <div class="produto">
                Produto 0:<br>
                <input type="text" name="produtos[0][quantidade]" placeholder="Quantidade de Itens" required>
                <input type="text" name="produtos[0][codigo_rodopar]" placeholder="Código do produto Rodopar" required> {{-- Não obrigatório --}}
                <input type="text" name="produtos[0][valor_unitario]" placeholder="Valor Unitário" required>
            </div>
        </div>

        <center><button type="button" class="btn-cadastrar" onclick="adicionarProduto()">Adicionar Produto</button></center> <br><br>

        <br>
        Nota Fiscal da operação de compra:<br>
        <input type="file" name="foto" id="foto" accept="image/*" required><br><br>

        <input type="text" name="informacoes_adicionais" placeholder="Informações Adicionais"><br><br>

    </div>

    <div id="campos-venda" class="tipo-campos" style="display:none;">

        <p class="center">(Venda de peças, venda de veículo)</p> <br>

            Tipo de Venda: <br>
    <select name="tipo_de_venda" id="tipo_de_venda" required>

        <option value=" "></option>
            <option value="veiculo">Venda de Veículo</option>
            <option value="peca">Venda de Peça</option>

      </select> <br>

          <span class="card-title center"><b>Dados da Solicitação</b></span>

          <input type="text" name="empresa_solicitante" placeholder="Empresa solicitante" required>
          <input type="text" name="cnpj" placeholder="CNPJ" required>

          <input type="text" name="cliente" placeholder="Cliente" required>
          <input type="text" name="cnpj_cliente" placeholder="CNPJ do Cliente" required> 
           <input type="text" name="codigo_cliente_rodopar" placeholder="Código Rodopar do Cliente"> {{-- Não obrigatório   --}}  <br>

          <span class="card-title center"><b>Itens</b></span>

          <div class="produtos">
              <div class="produto">
                  Produto 0:<br>
                  <input type="text" name="produtos[0][quantidade]" placeholder="Quantidade de Itens" required>
                  <input type="text" name="produtos[0][codigo_rodopar]" placeholder="Código do produto Rodopar" required> {{-- Não obrigatório   --}} <br>
                  <input type="text" name="produtos[0][valor_unitario]" placeholder="Valor Unitário" required>
              </div>
          </div>

          <center><button type="button" class="btn-cadastrar" onclick="adicionarProduto()">Adicionar Produto</button></center> <br><br>


          {{-- <input type="text" name="valor_nf" placeholder="Valor Total da Nota Fiscal"> --}}
          <input type="text" name="motivo_operacao" placeholder="Motivo da Operação"> {{-- Não obrigatorio  --}}

          <br>

          <input type="text" name="informacoes_adicionais" placeholder="Informações Adicionais"><br><br> {{-- Não obrigatorio  --}}

          Documento do veículo(para operação de venda de veículo):<br>
          <input type="file" name="foto" id="foto" accept="image/*" required><br><br>

    </div>


    <div id="campos-remessa" class="tipo-campos" style="display:none;">

        <p class="center">(Remessa de bem para conserto ou reparo,transferência de mercadoria, garantia, envio e/ou retorno de bem por conta de contrato de comodato)</p> <br><br>
        
      Tipo de Remessa: <br>
      <select name="tipo_de_venda" id="tipo_de_venda" required>

        <option value=" "></option>
            <option value="conserto">Bem para Conserto ou Reparo</option>
            <option value="transferencia">Transferência de Mercadoria</option>
            <option value="comodato">Envio e/ou Retorno de Bem por Conta de Contrato de Comodato</option>
            <option value="garantia">Garantia</option>

      </select> <br>

        <span class="card-title center"><b>Dados da Solicitação</b></span>

        <input type="text" name="empresa_solicitante" placeholder="Remetente(Emissor, Filial solicitante)" required>
        <input type="text" name="cnpj" placeholder="CNPJ" required>

        <input type="text" name="fornecedor" placeholder="Destinatário (Recebedor, Filial de envio da mercadoria)" required>
        <input type="text" name="codigo_fornecedor_rodopar" placeholder="Codigo Filial de Recebimento do Rodopar" required> {{-- Não obrigatorio  --}}
        <input type="text" name="cnpj_fornecedor" placeholder="CNPJ do Filial de Recebimento" required>
        
        <span class="card-title center"><b>Itens</b></span>

        <div class="produtos">
            <div class="produto">
                Produto 0:<br>
                <input type="text" name="produtos[0][quantidade]" placeholder="Quantidade de Itens" required>
                <input type="text" name="produtos[0][codigo_rodopar]" placeholder="Código do produto Rodopar"> {{-- Não obrigatorio  --}}
                <input type="text" name="produtos[0][valor_unitario]" placeholder="Valor Unitário" required>
            </div>
        </div>

        <center><button type="button" class="btn-cadastrar" onclick="adicionarProduto()">Adicionar Produto</button></center><br><br>
        
        <br>
        Nota Fiscal da operação de compra (remessa de garantia):<br>
        <input type="file" name="foto" id="foto" accept="image/*"><br><br>

        <input type="text" name="valor_nf" placeholder="Valor Total da Nota Fiscal" required>
        <input type="text" name="motivo_operacao" placeholder="Motivo da Operação" required>

        <br>

        <input type="text" name="informacoes_adicionais" placeholder="Informações Adicionais"><br> {{-- Não obrigatorio  --}}

    </div>

    <div id="campos-descarte" class="tipo-campos" style="display:none;">

        <span class="card-title center"><b>Dados da Solicitação</b></span>

        <input type="text" name="empresa_solicitante" placeholder="Remetente (Fornecedor, filial de envio)" required>
        <input type="text" name="cnpj" placeholder="CNPJ" required>

        <input type="text" name="fornecedor" placeholder="Destinatário (Cliente)" required>
        <input type="text" name="codigo_fornecedor_rodopar" placeholder="Codigo Fornecedor do Rodopar"> {{-- Não obrigatorio  --}}
        <input type="text" name="cnpj_fornecedor" placeholder="CNPJ do Fornecedor" required>
        
        <span class="card-title center"><b>Itens</b></span>

        <div class="produtos">
            <div class="produto">
                Produto 0:<br>
                <input type="text" name="produtos[0][quantidade]" placeholder="Quantidade de Itens" required>
                <input type="text" name="produtos[0][codigo_rodopar]" placeholder="Código do produto Rodopar" required> {{-- Não obrigatorio  --}}
                <input type="text" name="produtos[0][valor_unitario]" placeholder="Valor Unitário" required>
            </div>
        </div>

        <center><button type="button" class="btn-cadastrar" onclick="adicionarProduto()">Adicionar Produto</button></center>


        <input type="text" name="valor_nf" placeholder="Valor Total da Nota Fiscal" required>
        <input type="text" name="motivo_operacao" placeholder="Motivo da Operação" required> <br>

        <br>

        <input type="text" name="informacoes_adicionais" placeholder="Informações Adicionais"><br> {{-- Não obrigatorio  --}}

        <input type="text" name="n_de_fogo" placeholder="Nº de Fogo do Pneu" required><br>

    </div>
    


   <br><br>

    
    <input type="email" name="email_fiscal" placeholder="fiscal@grupocargopolo.com.br" disabled>

    <input type="email" name="email" placeholder="Email Solicitante (obrigatório)" required>

    {{-- <input type="email" name="email_gestor" placeholder="Email Gestor (obrigatório)" required> --}}

    Gestor Aprovador: <br>
    <select name="email_gestor" id="email_gestor" required>

        <option value=" "></option>
        @foreach ($aprovadores as $aprovador)
            <option value="{{$aprovador->email}}">{{$aprovador->filial}} - {{$aprovador->nome}}</option>
        @endforeach

    </select> <br>

    <input type="text" name="emails" placeholder="E-mails Adicionais (E-mails separados por ;)" >




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
  let index = 1;

  function adicionarProduto() {
      // Seleciona apenas o container visível
      const container = document.querySelector('.tipo-campos[style*="block"] .produtos');
      if (!container) return;

      const novo = document.createElement('div');
      novo.classList.add('produto');
      novo.innerHTML = `
          Produto ${index}:<br>
          <input type="text" name="produtos[${index}][quantidade]" placeholder="Quantidade de Itens">
          <input type="text" name="produtos[${index}][codigo_rodopar]" placeholder="Código do produto Rodopar">
          <input type="text" name="produtos[${index}][valor_unitario]" placeholder="Valor Unitário">
      `;
      container.appendChild(novo);
      index++;
  }
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
  
    const camposDevolucao = document.getElementById('campos-devolucao');
    const camposVenda = document.getElementById('campos-venda');
    const camposRemessa = document.getElementById('campos-remessa');
    const camposDescarte = document.getElementById('campos-descarte');

  
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
  
        if (valor === 'devolucao') {
          habilitarCampos(camposDevolucao);
        } else if (valor === 'venda') {
          habilitarCampos(camposVenda);
        } else if (valor === 'remessa') {
          habilitarCampos(camposRemessa);
        } else if (valor === 'descarte') {
          habilitarCampos(camposDescarte);
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
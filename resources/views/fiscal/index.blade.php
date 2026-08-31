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
      <select name="tipo_de_venda" id="tipo_de_remessa" required>

        <option value=" "></option>
            <option value="conserto">Bem para Conserto ou Reparo</option>
            <option value="transferencia">Transferência de Mercadoria</option>
            <option value="comodato">Envio e/ou Retorno de Bem por Conta de Contrato de Comodato</option>
            <option value="garantia">Garantia</option>

      </select> <br>


        <span class="card-title center"><b>Dados da Solicitação</b></span>

        <input type="text" name="empresa_solicitante" placeholder="Remetente(Emissor, Filial solicitante)" required>
        <input type="text" name="cnpj" placeholder="CNPJ" required><br>

        Estoque de Saida:
        <select name="cod_localizacao_saida" id="cod_localizacao_saida" class="browser-default" required>

            <option value="">Selecione a Unidade</option>
            @foreach($estoque->unique('DESCRI') as $filial)
                <option value="{{ $filial->CODIGO }}">{{ $filial->CODIGO }} - {{ $filial->DESCRI }}</option>
            @endforeach
        </select><br>

        <input type="text" name="fornecedor" placeholder="Destinatário (Recebedor, Filial de envio da mercadoria)" required>
        <input type="text" name="codigo_fornecedor_rodopar" placeholder="Codigo Filial de Recebimento do Rodopar" required> {{-- Não obrigatorio  --}}
        <input type="text" name="cnpj_fornecedor" placeholder="CNPJ do Filial de Recebimento" required>
                <br>
        Estoque de Entrada:
        <select name="cod_localizacao_entrada" id="cod_localizacao_entrada" class="browser-default" required>

            <option value="">Selecione a Unidade</option>
            @foreach($estoque->unique('DESCRI') as $filial)
                <option value="{{ $filial->CODIGO }}">{{ $filial->CODIGO }} - {{ $filial->DESCRI }}</option>
            @endforeach
        </select><br>


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

  // Função para adicionar produtos dinamicamente
  function adicionarProduto() {
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

  // Função Unificada para Esconder Campos
  function esconderTodosCampos() {
    document.querySelectorAll('.tipo-campos').forEach(div => {
        div.style.display = 'none';
        div.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = true;
            el.required = false;
        });
    });

    // Esconde especificamente os estoques e seus respectivos títulos (labels)
    const sSaida = document.getElementById('cod_localizacao_saida');
    const sEntrada = document.getElementById('cod_localizacao_entrada');
    
    [sSaida, sEntrada].forEach(el => {
        if(el) {
            el.style.display = 'none';
            el.disabled = true;
            // Esconde o texto "Estoque de..." que está antes do select
            if(el.previousElementSibling) el.previousElementSibling.style.display = 'none';
        }
    });
  }

  function habilitarCampos(div) {
    div.style.display = 'block';
    div.querySelectorAll('input, select, textarea').forEach(el => {
        // Não habilitamos os estoques aqui, eles têm regra própria abaixo
        if (el.id !== 'cod_localizacao_saida' && el.id !== 'cod_localizacao_entrada') {
            el.disabled = false;
        }
    });
  }

  // Listener para os botões principais (Devolução, Venda, Remessa...)
  document.querySelectorAll('.btn-group .btn').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        const valor = this.getAttribute('data-value');
        document.getElementById('tipo').value = valor;

        esconderTodosCampos();

        const targetDiv = document.getElementById('campos-' + valor);
        if(targetDiv) habilitarCampos(targetDiv);
    });
  });

  // Listener específico para o Tipo de Remessa (Transferência)
  document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'tipo_de_remessa') {
        const selectSaida = document.getElementById('cod_localizacao_saida');
        const selectEntrada = document.getElementById('cod_localizacao_entrada');
        const mostrar = (e.target.value === 'transferencia');

        [selectSaida, selectEntrada].forEach(el => {
            if(el) {
                const display = mostrar ? 'block' : 'none';
                el.style.display = display;
                el.disabled = !mostrar;
                el.required = mostrar;
                if(el.previousElementSibling) el.previousElementSibling.style.display = display;
            }
        });
    }
  });

  // Validações de e-mail e formulário
  function validarEmails() {
      const campo = document.getElementById('emails');
      const valor = campo.value.trim();
      if (valor === '') return true;
      const emails = valor.split(';').map(email => email.trim());
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const invalidos = emails.filter(email => !regex.test(email));
      if (invalidos.length > 0) {
          alert('E-mails inválidos:\n' + invalidos.join('\n'));
          return false;
      }
      return true;
  }

  function validarFormulario() {
    // Garante que campos desabilitados não barrem o envio por serem 'required'
    document.querySelectorAll('[required]').forEach(el => {
        if (el.disabled || el.offsetParent === null) {
            el.removeAttribute('required');
        }
    });
    return true;
  }

  window.addEventListener('load', esconderTodosCampos);
</script>




<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectGestor = document.getElementById('email_gestor');
    const emailVanderlei = 'vanderlei.nascimento@grupocargopolo.com.br';

    // Event Delegation: escuta o evento de mudança em qualquer lugar do formulário
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'tipo_de_venda') {
            const tipoVenda = e.target.value;

            if (tipoVenda === 'veiculo') {
                // 1. Tenta selecionar a opção do Vanderlei
                selectGestor.value = emailVanderlei;

                // Caso o e-mail não esteja cadastrado na lista $aprovadores, cria dinamicamente
                if (selectGestor.value !== emailVanderlei) {
                    const novaOpcao = new Option("Vanderlei Nascimento", emailVanderlei, true, true);
                    selectGestor.add(novaOpcao);
                    selectGestor.value = emailVanderlei;
                }

                // 2. Aplica trava visual e impede cliques/foco
                selectGestor.style.pointerEvents = 'none';
                selectGestor.style.backgroundColor = '#e9ecef';
                selectGestor.setAttribute('tabindex', '-1');

                // Impede alteração via teclado se o usuário forçar o foco
                selectGestor.onkeydown = function(evt) { evt.preventDefault(); };

            } else {
                // Destrava o campo se for "peca" ou vazio
                selectGestor.style.pointerEvents = 'auto';
                selectGestor.style.backgroundColor = '';
                selectGestor.removeAttribute('tabindex');
                selectGestor.onkeydown = null;
            }

            // 3. Se estiver usando Materialize CSS (re-inicializa a renderização visual do select)
            if (typeof M !== 'undefined' && M.FormSelect) {
                M.FormSelect.init(selectGestor);
            }
        }
    });
});
</script>



@endsection
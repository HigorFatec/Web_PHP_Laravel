@extends('layout')
@section('title', 'Fornecedor - Financeiro')
@section('conteudo')


<div class="row">
    <div class="col s12 m6 offset-m3">

        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! O Cadastro foi solicitado com sucesso!<br>
           </p>
          </div>
        </div>
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="card red darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Erro</span>
                        <p>{!! $error !!} <br>
                    </p>
                    </div>
                    </div>

            @endforeach
        @endif

        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Cadastro de Fornecedor</b></span>

    <form action="{{ route('fornecedor_financeiro.store') }}" method="POST">
        
        <span class="card-title center"><b>Selecione o tipo de Fornecedor:</b></span>

        <div class="btn-group center" role="group" aria-label="Tipo de Fornecedor">
            <input type="hidden" name="tipo" id="tipo" required>
            <button type="button" class="btn" data-value="juridico">Fornecedor Juridico</button>
            <button type="button" class="btn" data-value="fisico">Fornecedor Fisico</button>
        </div><br>
        <br>

        <div id="campos-fisico" class="tipo-campos" style="display:none;">

                    <h3><center><b>Cadastro do Fornecedor Físico</b></center></h3>

                    <h4><center>Informações do solicitante da compra</center></h4>
            @csrf

            <input type="text" id="nome_remetente" name="nome_remetente" placeholder="Nome do Solicitante da compra(obrigatório):" required>
            <br>

            <input type="email" id="email_remetente" name="email_remetente" placeholder="Email do Solicitante da compra(obrigatório):" required>


                <h4><center>Informações do Fornecedor</center></h4>



            <input type="text" id="razao_social" name="razao_social" placeholder="Nome Completo:" required>
            <br>

            <input type="text" id="nome_abreviado" name="nome_abreviado" placeholder="Nome Abreviado:" required>
            <br>


            <input type="text" id="cpf" name="cpf" placeholder="CPF" maxlength="11" pattern="\d{11}" required>
            <br>

            <input type="text" id="rg" name="rg" placeholder="RG" maxlength="9" pattern="\d{9}" required>
            <br>

            <input type="email" id="email_fornecedor" name="email_fornecedor" placeholder="E-mail Fornecedor" required>
            <br>


            <h4><center>Endereço</center></h4>

            <input type="text" id="endereco" name="endereco" placeholder="Endereço Completo:" required>
            <br>

            <input type="text" id="bairro" name="bairro" placeholder="Bairro:" required>
            <br>

            Cidade:
            <select name="cidade" id="cidade_select" class="browser-default" required>
              <option value = ""></option>
                @foreach ($cidades as $c)
                    <option value="{{ $c->CODMUN }}">{{ $c->DESCRI }}</option>
                @endforeach
            </select>

            <h4><center>Dados Bancários</center></h4>

                <input type="text" id="banco_2" name="banco" placeholder="Banco:" required><br>
                <input type="text" id="agencia_2" name="agencia" placeholder="Agência:" required><br>
                <input type="text" id="conta_2" name="conta" placeholder="Conta:" required><br>
                <input type="text" id="favorecido" name="favorecido" placeholder="Nome do Favorecido:" required><br>

            <h4><center>Dados Pix</center></h4>
                <input type="text" id="pix_aleatorio_2" name="pix_aleatorio" placeholder="Pix:"><br>

 
        </div>
        
        <div id="campos-juridico" class="tipo-campos" style="display:none;">
                <h3><center><b>Cadastro do Fornecedor Juridico</b></center></h3>
                @csrf
                <h4><center>Informações do solicitante da compra</center></h4>
                <p class = "preenchimento">

                <input type="text" id="nome_remetente" name="nome_remetente" placeholder="Nome do Solicitante da compra(obrigatório):" required>
                <br>

                <input type="email" id="email_remetente" name="email_remetente" placeholder="Email do Solicitante da compra(obrigatório):" required>
                </p>

                <h4><center>Informações do Fornecedor (obrigatório)</center></h4>

                <p class="preenchimento">

                <input type="text" id="razao_social" name="razao_social" placeholder="Razão Social:" required>
                <br>

                <input type="text" id="nome_abreviado" name="nome_abreviado" placeholder="Nome Abreviado:" required>
                <br>

                <input type="text" id="inscricao_estadual" name="ie"
                    placeholder="Inscrição Estadual:"
                    maxlength="14"
                    pattern="[0-9]{9,14}"
                    inputmode="numeric"
                    required>
                <br>

                <input type="text" id="cnpj" name="cnpj" placeholder="CNPJ:" required>
                <br>

                <input type="email" id="email_fornecedor" name="email_fornecedor" placeholder="E-mail Fornecedor" required>
                <br>

                </p>

                <h4><center>Endereço</center></h4>

                <p class="preenchimento">
                <input type="text" id="endereco" name="endereco" placeholder="Endereco Completo" required>
                <br>

                <input type="text" id="bairro" name="bairro" placeholder="Bairro:" required>
                <br>

                Cidade:
                <select name="cidade" id="cidade_select" class="browser-default" required>
                  <option value = ""></option>
                    @foreach ($cidades as $c)
                        <option value="{{ $c->CODMUN }}">{{ $c->DESCRI }}</option>
                    @endforeach
                </select>

                <h4><center>Dados Bancários</center></h4>

                <input type="text" id="banco" name="banco" placeholder="Banco:" required><br>
                <input type="text" id="agencia" name="agencia" placeholder="Agência:" required><br>
                <input type="text" id="conta" name="conta" placeholder="Conta:" required><br>
                <input type="text" id="favorecido" name="favorecido" placeholder="Nome do Favorecido:" required><br>

                <h4><center>Dados Pix</center></h4>
                <input type="text" id="pix_aleatorio" name="pix_aleatorio" placeholder="Pix Aleatório:"><br>

        </div>



        <center><button class="btn" type="submit" name="action">Enviar
            <i class="material-icons right">send</i>
          </button></center><br>

          
    </form>
</body>
</html>

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
  
    const camposAvista = document.getElementById('campos-fisico');
    const camposReembolso = document.getElementById('campos-juridico');
  
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
  
        if (valor === 'fisico') {
          habilitarCampos(camposAvista);
        } else if (valor === 'juridico') {
          habilitarCampos(camposReembolso);
        }
      });
    });
  
    // Opcional: ao carregar a página, esconder e desabilitar tudo
    window.addEventListener('load', () => {
      esconderTodosCampos();
    });
  </script>


<script>
document.getElementById('cpf').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('rg').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('banco').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('agencia').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('conta').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('inscricao_estadual').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('cnpj').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

document.getElementById('banco_2').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('agencia_2').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('conta_2').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('pix_cpf').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('pix_cpf_2').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('pix_telefone').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.getElementById('pix_telefone_2').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

</script>


    <script>
    $(document).ready(function() {
        $('#cidade_select').select2({
            placeholder: 'Selecione ou pesquise a cidade',
            width: '100%'
        });
    });
    </script>




@endsection
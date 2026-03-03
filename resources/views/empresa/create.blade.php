@extends('layout')
@section('title', 'Cadastro - Fornecedor')
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

        @if ($message = Session::get('success2'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! O Cadastro foi Aprovado com sucesso!<br>
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

      <form action="{{ route('empresa.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
        
        <span class="card-title center"><b>Selecione o tipo de Fornecedor:</b></span>

        <div class="btn-group center" role="group" aria-label="Tipo de Fornecedor">
            <input type="hidden" name="tipo" id="tipo" required>
            <button type="button" class="btn" data-value="juridico">Fornecedor Juridico</button>
            <button type="button" class="btn" data-value="fisico">Fornecedor Fisico</button>
        </div><br>
        <br>

        <div id="campos-fisico" class="tipo-campos" style="display:none;">

                    <span class="card-title center">Cadastro do Fornecedor Físico</b></span>

                    <span class="card-title center">Informações do solicitante da compra</span>
            @csrf

            <input type="text" id="nome_remetente" name="nome_remetente" placeholder="Nome do Solicitante da compra(obrigatório):" required>
            <br>

            <input type="email" id="email_remetente" name="email_remetente" placeholder="Email do Solicitante da compra(obrigatório):" required>


                <span class="card-title center">Informações do Fornecedor</span>



            <input type="text" id="razao_social" name="razao_social" placeholder="Nome Completo:" maxlength="80"  required>
            <br>

            <input type="text" id="nome_abreviado" name="nome_abreviado" placeholder="Nome Abreviado:" maxlength="40"  required>
            <br>


            <input type="text" id="cpf" name="cpf" placeholder="CPF" maxlength="11" pattern="\d{11}" required>
            <br>

            <input type="text" id="rg" name="rg" placeholder="RG" maxlength="11" pattern="\d{11}" required>
            <br>

            <input type="email" id="email_fornecedor" name="email_fornecedor" placeholder="E-mail Fornecedor" maxlength="80" required>
            <br>

            <input type="text" id="telefone_fornecedor_2" name="telefone_fornecedor" placeholder="Telefone Fornecedor" maxlength="16" required>
            <br>


            <span class="card-title center">Endereço</span>

            <input type="text" id="endereco" name="endereco" placeholder="Endereço Completo:" maxlength="80" required>
            <br>

            <input type="text" id="bairro" name="bairro" placeholder="Bairro:" maxlength="80"  required>
            <br>

            <input type="text" id="cep" name="cep" placeholder="Cep:" maxlength="8" required>
            <br>

            Cidade:
            <select name="cidade" id="cidade_select" class="browser-default" required>
              <option value = ""></option>
                @foreach ($cidades as $c)
                    <option value="{{ $c->CODMUN }}">{{ $c->DESCRI }}</option>
                @endforeach
            </select>

            <span class="card-title center">Dados Bancários</span>

            Banco:
            <select name="banco" id="banco_select" class="browser-default" required>
                <option value=""></option>
                @foreach ($bancos as $b)
                        <option value="{{ $b->CODBCO}}"> {{$b->CODBCO}} - {{$b->DESCRI}} </option>
                    
                @endforeach
            </select>

                <input type="text" id="agencia_2" name="agencia" placeholder="Agência:" required><br>
                <input type="text" id="conta_2" name="conta" placeholder="Conta:" required><br>
                <input type="text" id="favorecido" name="favorecido" placeholder="Nome do Favorecido:" required><br>

            <span class="card-title center">Dados Pix</span>
                Pix Preferencial:
                <select name="pix_preferencial" id="pix_preferencial">
                  <option value = ""></option>
                  <option value = "1">CPF/CNPJ</option>
                  <option value = "2">E-mail</option>
                  <option value = "3">Número Celular</option>
                  <option value = "4">Chave Aleatória</option>
                </select><br>
                <input type="text" id="pix_cnpj_2" name="pix_cnpj" placeholder="CPF/CNPJ"><br>
                <input type="email" id="pix_email_2" name="pix_email" placeholder="E-mail"><br>
                <input type="text" id="pix_telefone_2" name="pix_telefone" placeholder="Número Celular"><br>
                <input type="text" id="pix_aleatorio_2" name="pix_aleatorio" placeholder="Pix:"><br>

 
        </div>
        
        <div id="campos-juridico" class="tipo-campos" style="display:none;">
                <span class="card-title center"><b>Cadastro do Fornecedor Juridico</b></span>
                @csrf
                <span class="card-title center">Informações do solicitante da compra</span>
                <p class = "preenchimento">

                <input type="text" id="nome_remetente" name="nome_remetente" placeholder="Nome do Solicitante da compra(obrigatório):" required>
                <br>

                <input type="email" id="email_remetente" name="email_remetente" placeholder="Email do Solicitante da compra(obrigatório):" required>
                </p>

                <span class="card-title center">Informações do Fornecedor (obrigatório)</span>
                
                <p class="preenchimento">

                <input type="text" id="razao_social" name="razao_social" placeholder="Razão Social:" required>
                <br>

                <input type="text" id="nome_abreviado" name="nome_abreviado" placeholder="Nome Abreviado:" required>
                <br><br>


                Situação Inscrição Estadual:
                <select id="ie_status" required>
                    <option value="nao">NÃO ISENTO</option>
                    <option value="isento">ISENTO</option>
                </select>

                <input type="text" 
                      id="ie_input" 
                      placeholder="Inscrição Estadual" 
                      inputmode="numeric"
                      maxlength="14"
                      pattern="[0-9]{9,14}">
                <br><br>

                <!-- ESTE é o valor real enviado ao servidor -->
                <input type="hidden" id="ie" name="ie">





                <input type="text" id="cnpj" name="cnpj" placeholder="CNPJ:" required>
                <br>

                <input type="email" id="email_fornecedor" name="email_fornecedor" placeholder="E-mail Fornecedor" required>
                <br>

                <input type="text" id="telefone_fornecedor" name="telefone_fornecedor" placeholder="Telefone Fornecedor" maxlength="16" required>

                </p>

                <span class="card-title center">Endereço</span>

                <p class="preenchimento">
                <input type="text" id="endereco" name="endereco" placeholder="Endereco Completo" required>
                <br>

                <input type="text" id="bairro" name="bairro" placeholder="Bairro:" required>
                <br>

                <input type="text" id="cep_2" name="cep" placeholder="Cep:" maxlength="8" required>
                <br>
                    

                Cidade:
                <select name="cidade" id="cidade_select" class="browser-default" required>
                  <option value = ""></option>
                    @foreach ($cidades as $c)
                        <option value="{{ $c->CODMUN }}">{{ $c->DESCRI }}</option>
                    @endforeach
                </select>

                <span class="card-title center">Dados Bancários</span>

                Banco:
                <select name="banco" id="banco_select" class="browser-default" required>
                    <option value=""></option>
                    @foreach ($bancos as $b)
                            <option value="{{ $b->CODBCO}}"> {{$b->CODBCO}} -  {{$b->DESCRI}} </option>
                        
                    @endforeach
                </select>


                <input type="text" id="agencia" name="agencia" placeholder="Agência:" ><br>
                <input type="text" id="conta" name="conta" placeholder="Conta:" ><br>
                <input type="text" id="favorecido" name="favorecido" placeholder="Nome do Favorecido:" ><br>

                <span class="card-title center">Dados Pix</span>
                Pix Preferencial:
                <select name="pix_preferencial" id="pix_preferencial">
                  <option value = ""></option>
                  <option value = "1">CPF/CNPJ</option>
                  <option value = "2">E-mail</option>
                  <option value = "3">Número Celular</option>
                  <option value = "4">Chave Aleatória</option>
                </select><br>
                <input type="text" id="pix_cnpj" name="pix_cnpj" placeholder="CPF/CNPJ"><br>
                <input type="email" id="pix_email" name="pix_email" placeholder="E-mail"><br>
                <input type="text" id="pix_telefone" name="pix_telefone" placeholder="Número Celular"><br>
                <input type="text" id="pix_aleatorio" name="pix_aleatorio" placeholder="Chave Aleatório:"><br>

        </div>



        <center><button class="btn" type="submit" name="action">Enviar
            <i class="material-icons right">send</i>
          </button></center><br>

          
    </form>
</body>
</html>


<script>
const select = document.getElementById("ie_status");
const input = document.getElementById("ie_input");
const hidden = document.getElementById("ie");

// Quando trocar o select
select.addEventListener("change", () => {
    if (select.value === "isento") {
        input.value = "ISENTO";
        input.readOnly = true;
        input.removeAttribute("required");
        hidden.value = "ISENTO";       // <<< valor enviado ao servidor
    } else if (select.value === "nao") {
        input.value = "";
        input.readOnly = false;
        input.setAttribute("required", "required");
        hidden.value = "";             // será atualizado conforme digita
        input.focus();
    }
});

// Quando digitar no input (caso NÃO ISENTO)
input.addEventListener("input", () => {
    // Só números
    input.value = input.value.replace(/\D/g, "");
    hidden.value = input.value;
});
</script>



<script>
    // CPF ou CNPJ automático
    document.getElementById("pix_cnpj").addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "");

        if (v.length <= 11) {
            // CPF: xxx.xxx.xxx-xx
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        } else {
            // CNPJ: xx.xxx.xxx/xxxx-xx
            v = v.replace(/^(\d{2})(\d)/, "$1.$2");
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
            v = v.replace(/\.(\d{3})(\d)/, ".$1/$2");
            v = v.replace(/(\d{4})(\d)/, "$1-$2");
        }

        this.value = v;
    });

    // Telefone: (xx) xxxxx-xxxx
    document.getElementById("pix_telefone").addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "");
        v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
        v = v.replace(/(\d{5})(\d)/, "$1-$2");
        this.value = v;
    });

    // Chave aleatória → deixar só caracteres válidos
    document.getElementById("pix_aleatorio").addEventListener("input", function () {
        this.value = this.value.replace(/[^a-zA-Z0-9-]/g, "");
    });
</script>

<script>
    // CPF ou CNPJ automático
    document.getElementById("pix_cnpj_2").addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "");

        if (v.length <= 11) {
            // CPF: xxx.xxx.xxx-xx
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        } else {
            // CNPJ: xx.xxx.xxx/xxxx-xx
            v = v.replace(/^(\d{2})(\d)/, "$1.$2");
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
            v = v.replace(/\.(\d{3})(\d)/, ".$1/$2");
            v = v.replace(/(\d{4})(\d)/, "$1-$2");
        }

        this.value = v;
    });


    // Telefone: (xx) xxxxx-xxxx
    document.getElementById("pix_telefone_2").addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "");
        v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
        v = v.replace(/(\d{5})(\d)/, "$1-$2");
        this.value = v;
    });

    // Chave aleatória → deixar só caracteres válidos
    document.getElementById("pix_aleatorio_2").addEventListener("input", function () {
        this.value = this.value.replace(/[^a-zA-Z0-9-]/g, "");
    });

        // Telefone: (xx) xxxxx-xxxx
    document.getElementById("telefone_fornecedor").addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "");

        // limita a 12 números: 000000000000
        v = v.substring(0, 12);

        // (000)
        if (v.length > 3) {
            v = v.replace(/^(\d{3})(\d)/, "($1)$2");
        }

        // (000)00000-0000
        if (v.length > 8) {
            v = v.replace(/(\d{5})(\d{1,4})$/, "$1-$2");
        }

        this.value = v;
    });


            // Telefone: (xx) xxxxx-xxxx
    document.getElementById("telefone_fornecedor_2").addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "");

        // limita a 12 números: 000000000000
        v = v.substring(0, 12);

        // (000)
        if (v.length > 3) {
            v = v.replace(/^(\d{3})(\d)/, "($1)$2");
        }

        // (000)00000-0000
        if (v.length > 8) {
            v = v.replace(/(\d{5})(\d{1,4})$/, "$1-$2");
        }

        this.value = v;
    });



        document.getElementById("telefone_fornecedor").addEventListener("keydown", function (e) {
    if (e.key === " ") {
        e.preventDefault();
    }
});


document.getElementById("telefone_fornecedor_2").addEventListener("keydown", function (e) {
    if (e.key === " ") {
        e.preventDefault();
    }
});

document.getElementById("cep").addEventListener("input", function () {
    let v = this.value.replace(/\D/g, "");

    if (v.length > 5) {
        v = v.replace(/^(\d{2})(\d{3})(\d{3})/, "$1.$2-$3");
    }

    this.value = v;
});

document.getElementById("cep_2").addEventListener("input", function () {
    let v = this.value.replace(/\D/g, "");

    if (v.length > 5) {
        v = v.replace(/^(\d{2})(\d{3})(\d{3})/, "$1.$2-$3");
    }

    this.value = v;
});


</script>

<script>
    function aplicarMascaraTelefone(input) {
        input.addEventListener("input", function () {
            let v = this.value.replace(/\D/g, "");

            if (!v.startsWith("55")) {
                v = "55" + v;
            }

            v = v.substring(0, 13);

            v = v.replace(/^55(\d{2})(\d{5})(\d{0,4}).*/, "+55 ($1) $2-$3");

            this.value = v;
        });

        input.addEventListener("keydown", function (e) {
            if (this.selectionStart < 4 && (e.key === "Backspace" || e.key === "Delete")) {
                e.preventDefault();
            }
        });

        input.value = "+55 ";
    }

    aplicarMascaraTelefone(document.getElementById("pix_telefone"));
    aplicarMascaraTelefone(document.getElementById("pix_telefone_2"));
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

    <script>
    $(document).ready(function() {
        $('#banco_select').select2({
            placeholder: 'Selecione ou pesquise o banco',
            width: '100%'
        });
    });
    </script>



@endsection
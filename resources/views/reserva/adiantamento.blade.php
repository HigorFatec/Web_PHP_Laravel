@extends('layout')
@section('title', 'Adiantamento de Viagem')
@section('conteudo')


<div class="row">
<div class="container">

    @if ($message = Session::get('success'))
    <div class="card green darken-1 center">
        <div class="card-content white-text">
            <span class="card-title">Adiantamento solicitado com sucesso!</span>
            <p>Parabéns! O seu adiantamento foi solicitado com sucesso!<br>
                Acesse a aba "Minhas Reservas" para visualizar a sua solicitação.
            </p>
        </div>
    </div>
    @endif

  @if ($message = Session::get('error_dias'))
  <div class="card red darken-1">
    <div class="card-content white-text">
      <span class="card-title">Erro!</span>
      <p>Para fazer uma solicitação de adiantamento de viagem<br>
         É necessário 5 dias ou mais de viagem!
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
  </div>
@endif

<div class="container">
<div class="card">
  <div class="card-content">
      <span class="card-title center"><b>Adiantamento de Viagem</b></span><br>

<form action="/reserva/adiantamento" method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf
          

    Destino: <br>
      <select name="destino" id="cidade_select" class="browser-default" required>
        <option value = ""></option>
          @foreach ($cidades as $c)
              <option value="{{ $c->DESCRI }}">{{ $c->DESCRI }} - {{ $c->ESTADO }}</option>
          @endforeach
      </select><br><br>


    Data de inicio de viagem:
    <input type="date" id="ida" name="ida" placeholder="Data" required><br><br>
    Data do fim da viagem:
    <input type="date" id="volta" name="volta" placeholder="Data" required>

    Motivo da Viagem:
    <input type="text" name="motivo" placeholder="Motivo da Viagem" required>

    Validado pelo Gestor:
    <input type="text" name="validacao" placeholder="Validado pelo Gestor (autorização)" required>

    Email do Gestor:
    <input type="email" name="email_gestor" placeholder="Email do Gestor" required>
    <input type="text" name="observacoes" placeholder="Observações (não necessariamente)"><br><br>



            <span class="card-title center"><b>Dados do Funcionário</b></span>
            <center><span class="card-description">Obs:<b>No rodopar</b> precisa estar cadastrado como <b>funcionário</b></span></center><br>
        <center><span>Não encontrou o funcionário? <a href="https://suporte.grupocargopolo.com.br:13004/WOListView.do">Clique aqui para abrir um chamado para cadastro</a></span></center>

        Funcionário: <br>
        <input type="text" id="search_fornecedor" placeholder="Buscar fornecedor... (Razão Social, Codigo Rodopar, CPF/CNPJ RESPEITANDO A PONTUAÇÃO !!!! )">

        <select name="fornecedor" id="fornecedor_select" class="browser-default" required>
            <option value="">Selecione...</option>
        </select><br><br>

    Nome Completo:
    <input type="text" id="name_input" name="nome" placeholder="Nome Completo" required>
    E-mail:
    <input type="email" name="email" placeholder="E-mail" required>
    CPF:
    <input type="text" id="cnpj_input" name="cpf" placeholder="CPF" required>
    RG:
    <input type="text" id="rg_input" name="rg" placeholder="RG" required><br><br>
    Data de Nascimento:
    <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required><br><br>
    

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
            <option value="{{ $filial->email_gestor }}" data-unidade="{{ $filial->cod_unidade }}" data-custo="{{ $filial->cod_custo }}">
                {{ $filial->nome_gestor }}
            </option>
        @endforeach
    </select><br><br><br><br>


    <span class="card-title center"><b>Dados Bancários</b></span>

    Banco:
    <input type="text" id="banco_input" name="banco" placeholder="Banco" required>

    Agência:
    <input type="number" id="agencia_input" name="agencia" placeholder="Agência" required>

    Conta: 
    <input type="text" id="conta_input" name="conta" placeholder="Conta" required>

    Tipo de Conta:
    <input type="text" name="tipo_conta" placeholder="Tipo de Conta" required>

    Titular da Conta:
    <input type="text" id="favorecido_input" name="titular" placeholder="Titular da Conta" required>

    Pix: <i> (Obs <b>se estiver em branco, peça para cadastrarem no rodopar</b>)</i> <br>
    <input type="text" name="pix" id="pix_input" placeholder="PIX" readonly><br><br>

    Tipo de Chave Pix:
    <input type="text" 
    name="tipo_pix" 
    id="tipo_pix_input" 
    class="form-control" 
    readonly 
    placeholder="Selecione um fornecedor..." 
    style="background-color: #f8f9fa; cursor: not-allowed;">

    <br><br>

    <br>
    
    Valor:
    <input type="text" id="valor" name="valor" placeholder="Valor"required><br><br>

    Anexar Documento(CNH ou RG):<br>
    <input type="file" name="foto" id="foto" accept="image/*"><br><br>


    <a href="{{route('reserva.home')}}">
      <button type="button" class="btn-cadastrar left">Voltar</button></a>
    <!-- Outros campos aqui -->
    <button type="submit" class="btn-cadastrar right">Enviar</button><br><br>
  </form>
</div>
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
            url: '/adiantamentos/buscar',
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
                            data-rg="${f.rg ?? ''}"
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

        var rg = $opt.data('rg') || '';
        $('#rg_input').val(rg);
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
// 1. Quando a Filial muda (Filtro Geral)
document.getElementById('filial_select').addEventListener('change', function () {
    let unidade = this.value;

    const seletores = ['#centro_custo_select', '#centro_gasto_select', '#gestor_aprovador'];
    
    seletores.forEach(id => {
        document.querySelectorAll(`${id} option`).forEach(opt => {
            if (opt.value === "") return;
            let pertenceAUnidade = opt.getAttribute('data-unidade') === unidade;
            opt.hidden = !pertenceAUnidade;
            opt.disabled = !pertenceAUnidade;
        });
        document.querySelector(id).value = ""; // Limpa seleções anteriores
    });
});

// 2. Quando o Centro de Custo muda (Refina o Gestor)
document.getElementById('centro_custo_select').addEventListener('change', function () {
    let unidadeSelecionada = document.getElementById('filial_select').value;
    let custoSelecionado = this.value;

    document.querySelectorAll('#gestor_aprovador option').forEach(opt => {
        if (opt.value === "") return;

        // Pega os dados do gestor
        let gestorUnidade = opt.getAttribute('data-unidade');
        let gestorCusto = opt.getAttribute('data-custo');

        // Só mostra se pertencer à unidade E ao custo selecionado
        let deveExibir = (gestorUnidade === unidadeSelecionada && gestorCusto === custoSelecionado);

        opt.hidden = !deveExibir;
        opt.disabled = !deveExibir;
    });

    document.getElementById('gestor_aprovador').value = ""; // Reseta o gestor
});
</script>



@endsection
@extends('layout')
@section('title', 'Ajuda de Custo')
@section('conteudo')

@if (@auth()->user()->id != null)


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

    @if ($message = Session::get('aprovado'))
    <div class="card green darken-1">
        <div class="card-content white-text">
        <span class="card-title">Sucesso!</span>
        <p>Parabéns! A Solicitação foi aprovada com sucesso!<br>
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
      <span class="card-title center"><b>Ajuda de Custo</b></span><br>

<form id="form-financeiro" action="{{route('ajuda_custo.store')}}"method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf





            <span class="card-title center"><b>Dados do Funcionário</b></span>
            <center><span class="card-description">Obs:<b>No rodopar</b> precisa estar cadastrado como <b>funcionário</b></span></center><br>
        <center><span>Não encontrou o funcionário? <a href="https://suporte.grupocargopolo.com.br:13004/WOListView.do">Clique aqui para abrir um chamado para cadastro</a></span></center>

        Funcionário: <br>
        <input type="text" id="search_fornecedor" placeholder="Buscar fornecedor... (Razão Social, Codigo Rodopar, CPF/CNPJ <b>RESPEITANDO A PONTUAÇÃO</b> !!!! )">

        <select name="fornecedor" id="fornecedor_select" class="browser-default" required>
            <option value="">Selecione...</option>
        </select><br><br>


        <input type="text" id="cnpj_input" name="cnpj" placeholder="CNPJ/CPF" readonly>
        <input type="text" id="name_input" name="name" placeholder="Nome do Fornecedor">

        <span class="card-title center"><b>Dados bancários</b></span>

        <input type="text" id="banco_input" name="banco" placeholder="Código do Banco">
        <input type="number" id ="agencia_input" name="agencia" placeholder="Agencia">
        <input type="number" id="conta_input" name="conta" placeholder="Conta">

        Favorecido (quem irá receber):<br>
        <input type="text" id="favorecido_input" name="favorecido" placeholder="Nome do Favorecido"><br>

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

        <span class="card-title center"><b>Valores R$</b></span>

        
        Valor ajuda de custo fixo:
        <input type="text" id="valor" name="valor_fixo" placeholder="Valor Fixo">

        Valor ajuda de custo proporcional:
        <input type="text" id="valor_1" name="valor_proporcional" placeholder="Valor que irá ser pago"><br>

         Observações:
        <input type="text" name="observacao" placeholder="Observações"><br>

        Data Admissão:
        <input type="date" name="data_admissao" placeholder="Data de Admissão"><br><br>


 
    
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
    </select><br><br>







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
    // if (typeof $.fn.select2 === 'function') {
    //     if (!$('#fornecedor_select').hasClass('select2-applied')) {
    //         $('#fornecedor_select').select2({
    //             placeholder: 'Selecione ou pesquise o fornecedor',
    //             width: '100%'
    //         }).addClass('select2-applied');
    //     }
    // }

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
    function aplicarMascaraDecimal(idCampo) {
        const campo = document.getElementById(idCampo);

        // Bloqueia qualquer caractere que não seja número, ponto ou vírgula
        campo.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9.,]/g, "");
        });

        campo.addEventListener('blur', function () {
            // 1. Troca vírgula por ponto para garantir que o parseFloat entenda
            let valorRaw = this.value.replace(",", ".");
            
            // 2. Converte para número
            let floatVal = parseFloat(valorRaw);

            // 3. Se for um número válido, fixa em 2 casas com ponto decimal
            if (!isNaN(floatVal)) {
                this.value = floatVal.toFixed(2);
            } else {
                this.value = ""; 
            }
        });
    }

    // Inicializa os campos
    aplicarMascaraDecimal('valor');
    aplicarMascaraDecimal('valor_1');
</script>





<script>
document.getElementById('unidade_negocio_select').addEventListener('change', function () {
    // 1. Pega o valor da Unidade de Negócio selecionada (CODUNN)
    let unidadeSelecionada = this.value; 
    
    // 2. Referência para o select de Gestores
    let gestorSelect = document.getElementById('gestor_aprovador');
    let optionsGestor = gestorSelect.querySelectorAll('option');

    // 3. Itera sobre as opções de gestores
    optionsGestor.forEach(opt => {
        // Ignora a opção vazia (Ex: "Selecione...")
        if (opt.value === "") return;

        // Pega o código da unidade que está no data-unidade do Gestor
        let unidadeDoGestor = opt.getAttribute('data-unidade');

        // Compara os valores. Se for igual, mostra. Se não, esconde.
        // Usamos '==' para que '01' seja igual a 1, caso haja diferença de tipos.
        if (unidadeDoGestor == unidadeSelecionada) {
            opt.hidden = false;
            opt.disabled = false;
        } else {
            opt.hidden = true;
            opt.disabled = true;
        }
    });

    // 4. Reseta o valor do gestor para vazio toda vez que a unidade mudar
    gestorSelect.value = ""; 
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





@endsection
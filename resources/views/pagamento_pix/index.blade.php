@extends('layout')
@section('title', 'Pagamento Pix')
@section('conteudo'),



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

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="card red darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Erro</span>
                        <p>{{$error}} <br>
                    </p>
                    </div>
                    </div>

            @endforeach
        @endif

        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Lançamento do Abastecimento</b></span>



<form action="{{route('pagamento_pix.store')}}"method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf



            <span class="card-title center"><b>Dados do Motorista</b></span>
            <center><span class="card-description">Obs:<b>No rodopar</b> precisa estar cadastrado como <b>funcionário</b></span></center><br>
        <center><span>Não encontrou o motorista ? Procure o RH e peça para cadastra-lo.</span></center>

        Motorista: <br>
        <input type="text" id="search_fornecedor" placeholder="Buscar fornecedor... (Razão Social, Codigo Rodopar, CPF/CNPJ <b>RESPEITANDO A PONTUAÇÃO</b> !!!! )">

        <select name="fornecedor" id="fornecedor_select" class="browser-default" required>
            <option value="">Selecione...</option>
        </select><br><br>

        <input type="text" id="cnpj_input" name="cpf" placeholder="CNPJ/CPF" readonly>
        <input type="text" id="name_input" name="name" placeholder="Nome do Fornecedor">


        <span class="card-title center"><b>Valores R$</b></span>

        Valor (R$): <br>
        <input type="text" id="valor" name="valor" placeholder="Valor R$"><br>

        Quantidade (L): <br>
        <input type="text" id="valor_1" name="litragem" placeholder="Litragem"><br>

        Veiculo: <br>
        <select name="placa" class="browser-default" required>
            <option value=""></option>
            @foreach ($veiculos->unique('NUMVEI') as $v)
                <option value="{{ $v->CODVEI }}">
                    {{ $v->NUMVEI }}
                </option>
            @endforeach
        </select><br>

        Posto: <br>
        <select name="cnpj" class="browser-default" required>
            <option value=""></option>
            @foreach ($postos->unique('CODCGC') as $p)
                <option value="{{ $p->CODPON }}">
                    {{ $p->CODCGC }} - {{ $p->DESCRI }}
                </option>
            @endforeach
        </select><br>


    <br>
    E-mail: <br> <input type="email" name="email" id="email" required><br>
    E-mail do Gestor: <br> <input type="email" name="email_gestor" id="email_gestor" required>
    Data/Hora: <br> <input type="text" name="data" id="data" required> <br>
    Número do Cupom: <br> <input type="text" name="cupom" id="cupom" required> <br>
    KM do veiculo: <br> <input type="number" name="km" id="km" required> <br>
    Nome do posto: <br><input type="text" name="posto" id="posto" required><br>

    Filial: <br>
    <select name="filial" id="filial" required>

        <option value=" "></option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach


    </select> <br>

    Produto: <br>
    <select name="produto" id="produto" required>

        <option value=" "></option>
        @foreach ($produtos as $produto)
            <option value="{{$produto}}">{{$produto}}</option>
        @endforeach


    </select> <br>


    Produto ARLA-32 (caso não haja informar "0"): <br><input type="text" name="produto_arla" id="produto_arla" required>
    Litragem ARLA-32 (caso não haja informar "0") : <br><input type="text" name="litragem_arla" id="litragem_arla" required>
    Valor ARLA-32 (caso não haja informar "0"): <br><input type="text" name="valor_arla" id="valor_arla" required>

    <span class="card-title center"><b>Dados Bancários</b></span><br>

    Banco: <br><input type="text" name="banco" id="banco" required>
    Agência: <br><input type="text" name="agencia" id="agencia" required>
    Conta Corrente: <br><input type="text" name="conta" id="conta" required>
    CNPJ: <br><input type="text" name="cnpj_2" id="cnpj_2" maxlength="14" pattern="\d{14}"  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 14);"  required>
    Favorecido: <br><input type="text" name="favorecido" id="favorecido" required>
    Chave Pix: <br><input type="text" name="pix" id="pix" required>
    Valor: <br><input type="text" name="valor_3" id="valor_3" required><br><br><br>

    Anexar Nota Fiscal: <br>
    <input type="file" name="foto" id="foto" accept="image/*" ><br><br>



    <button type="submit" class="btn-cadastrar">Enviar</button>

    <a href="{{route('reserva.sobre')}}">
        <button type="button" class="btn-cadastrar right">Sobre</button></a>
    
    <br>

</form>

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
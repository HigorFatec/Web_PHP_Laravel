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
        <input type="text" id="name_input" name="name" placeholder="Nome do Fornecedor" readonly>


        <span class="card-title center"><b>Valores R$</b></span>

        Produto: <br>
        <select name="produto" id="produto" required>

            <option value=" "></option>
            @foreach ($produtos as $produto)
                <option value="{{$produto->id}}">{{$produto->nome}}</option>
            @endforeach


        </select> <br>

        Valor Total (R$): <br>
        <input type="text" id="valor" name="valor" placeholder="Valor R$"><br>

        Quantidade (L): <br>
        <input type="text" id="valor_1" name="litragem" placeholder="Litragem"><br>

        Veiculo: <br>
        <input type="text" id="search_veiculo" placeholder="Digite a placa para buscar... (Ex: ABC1234)">

        <select name="placa" id="veiculo_select" class="browser-default" required>
            <option value="">Selecione um veículo...</option>
            @foreach ($veiculos->unique('NUMVEI') as $v)
                <option value="{{ $v->CODVEI }}" data-numvei="{{ strtoupper($v->NUMVEI) }}">
                    {{ $v->NUMVEI }}
                </option>
            @endforeach
        </select>
        <span id="veiculo_erro" style="color: red; display: none; font-weight: bold;">Veículo não encontrado!</span>
        <br>

        Posto: <br>
        <input type="text" id="search_posto" placeholder="Digite o CNPJ do posto para buscar... (Apenas números ou com pontos)">

        <select name="cnpj" id="posto_select" class="browser-default" required>
            <option value="">Selecione um posto...</option>
            @foreach ($postos->unique('CODCGC') as $p)
                <option value="{{ $p->CODPON }}" data-codcgc="{{ preg_replace('/\D/', '', $p->CODCGC) }}">
                    {{ $p->CODCGC }} - {{ $p->DESCRI }}
                </option>
            @endforeach
        </select>
        <span id="posto_erro" style="color: red; display: none; font-weight: bold;">Posto não encontrado!</span>
        <br>

    <br>
    E-mail: <br> <input type="email" name="email" id="email" required><br>
    E-mail do Gestor: <br> <input type="email" name="email_gestor" id="email_gestor" required>
    Data/Hora: <br> <input type="text" name="data" id="data" required> <br>
    Número do Cupom: <br> <input type="text" name="cupom" id="cupom" required> <br>
    KM do veiculo: <br> <input type="text" name="km" id="km" required> <br>
    Nome do posto: <br><input type="text" name="posto" id="posto" required><br>

    Filial: <br>
    <select name="filial" id="filial" required>

        <option value=" "></option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach


    </select> <br>



    <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="col s12">
            <label style="font-size: 16px; color: #000; font-weight: bold;">
                Possui abastecimento de produto ARLA-32?
            </label>
            <div class="switch" style="margin-top: 10px;">
                <label>
                    Não
                    <input type="checkbox" id="possui_arla">
                    <span class="lever"></span>
                    Sim
                </label>
            </div>
        </div>
    </div>

    <div id="container_arla" style="display: none;">
        Produto ARLA-32: <br>
        <select name="produto_arla" id="produto_arla">

            <option value=" "></option>
            @foreach ($produtos as $produto)
                <option value="{{$produto->id}}">{{$produto->nome}}</option>
            @endforeach


        </select> <br>


        Litragem ARLA-32: <br>
        <input type="text" name="litragem_arla" id="litragem_arla"><br>

        Valor ARLA-32: <br>
        <input type="text" name="valor_arla" id="valor_arla"><br>
    </div>

    <span class="card-title center"><b>Dados Bancários</b></span><br>

    Banco: <br><input type="text" name="banco" id="banco" required>
    Agência: <br><input type="text" name="agencia" id="agencia" required>
    Conta Corrente: <br><input type="text" name="conta" id="conta" required>
    CNPJ: <br><input type="text" name="cnpj_2" id="cnpj_2" maxlength="14" pattern="\d{14}"  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 14);"  required>
    Favorecido: <br><input type="text" name="favorecido" id="favorecido" required>
    Chave Pix: <br><input type="text" name="pix" id="pix" required>
    Valor Total: <br><input type="text" name="valor_3" id="valor_3" required><br><br><br>

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
            url: '/pagamento_pix/buscar',
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
                        <option value="${f.codmot}"
                            data-numcpf="${f.numcpf ?? ''}"
                            data-nommot="${f.nommot ?? ''}">
                            ${f.codmot} - ${f.nommot} - ${f.numcpf}
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
        var cnpj = $opt.data('numcpf') || '';
        $('#cnpj_input').val(cnpj);
        var name = $opt.data('nommot') || '';
        $('#name_input').val(name);
    
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
    aplicarMascaraDecimal('litragem_arla');
    aplicarMascaraDecimal('valor_3');
    aplicarMascaraDecimal('valor_arla');
    aplicarMascaraDecimal('km');
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



<script>

$(function () {
    const $inputVeiculo = $('#search_veiculo');
    const $selectVeiculo = $('#veiculo_select');
    const $erroVeiculo = $('#veiculo_erro');

    // Guarda todas as opções originais do select para não perdê-las ao filtrar
    const $opcoesOriginais = $selectVeiculo.find('option').clone();

    $inputVeiculo.on('input', function () {
        // Pega o texto digitado e transforma em maiúsculo (padrão de placa)
        let termo = $(this).val().trim().toUpperCase();

        // Se o campo estiver vazio, restaura o select original e limpa erros
        if (termo === "") {
            $selectVeiculo.empty().append($opcoesOriginais.clone());
            $selectVeiculo.val(""); // Reseta a seleção
            $erroVeiculo.hide();
            return;
        }

        // Filtra as opções originais que correspondem ao que foi digitado
        // Procura tanto no atributo data-numvei quanto no texto visível
        let $opcoesFiltradas = $opcoesOriginais.filter(function () {
            let numvei = $(this).data('numvei') || '';
            let texto = $(this).text().toUpperCase();
            return numvei.includes(termo) || texto.includes(termo) || $(this).val() === "";
        });

        // Atualiza o select com as opções filtradas
        $selectVeiculo.empty().append($opcoesFiltradas);

        // Se encontrou apenas 1 veículo (além da opção vazia "Selecione..."), já seleciona ele automaticamente
        if ($opcoesFiltradas.length === 2 && $opcoesFiltradas.eq(1).val() !== "") {
            $selectVeiculo.val($opcoesFiltradas.eq(1).val());
            $erroVeiculo.hide();
        } 
        // Se não encontrou nenhum veículo correspondente
        else if ($opcoesFiltradas.length <= 1) { 
            $selectVeiculo.val(""); // Força o select a ficar vazio (ativando o 'required')
            $erroVeiculo.show();    // Exibe a mensagem de erro na tela
        } 
        // Se achou múltiplos, deixa o usuário escolher na lista filtrada
        else {
            $selectVeiculo.val("");
            $erroVeiculo.hide();
        }
    });
});


$(function () {
    const $inputPosto = $('#search_posto');
    const $selectPosto = $('#posto_select');
    const $erroPosto = $('#posto_erro');

    // Salva as opções originais do select de postos
    const $opcoesPostosOriginais = $selectPosto.find('option').clone();

    $inputPosto.on('input', function () {
        // Limpa o termo digitado mantendo apenas os números para comparar perfeitamente
        let termo = $(this).val().replace(/\D/g, '');

        // Se o campo de busca for limpo, restaura o select original
        if (termo === "") {
            $selectPosto.empty().append($opcoesPostosOriginais.clone());
            $selectPosto.val(""); 
            $erroPosto.hide();
            return;
        }

        // Filtra as opções originais comparando o CODCGC salvo no data-codcgc
        let $opcoesFiltradas = $opcoesPostosOriginais.filter(function () {
            let codcgc = $(this).data('codcgc') ? String($(this).data('codcgc')) : '';
            return codcgc.includes(termo) || $(this).val() === "";
        });

        // Atualiza o select de postos com o resultado do filtro
        $selectPosto.empty().append($opcoesFiltradas);

        // Se encontrar exatamente 1 posto correspondente, já seleciona ele automaticamente
        if ($opcoesFiltradas.length === 2 && $opcoesFiltradas.eq(1).val() !== "") {
            $selectPosto.val($opcoesFiltradas.eq(1).val());
            $erroPosto.hide();
        } 
        // Se digitou algo e não achou nada, zera o select e exibe erro (o required vai travar o envio)
        else if ($opcoesFiltradas.length <= 1) {
            $selectPosto.val("");
            $erroPosto.show();
        } 
        // Se houver mais de um resultado parcial, deixa aberto para o usuário escolher no select
        else {
            $selectPosto.val("");
            $erroPosto.hide();
        }
    });
});

</script>

<script>
$(function () {
    const $checkboxArla = $('#possui_arla');
    const $containerArla = $('#container_arla');
    
    const $inputProduto = $('#produto_arla');
    const $inputLitragem = $('#litragem_arla');
    const $inputValor = $('#valor_arla');

    // Escuta a mudança no switch/checkbox
    $checkboxArla.on('change', function () {
        if ($(this).is(':checked')) {
            // Se SIM: Mostra os campos
            $containerArla.fadeIn();
            
            // Limpa o "0" padrão se o usuário for digitar, ou deixa em branco para ele preencher
            if ($inputProduto.val() === "0") $inputProduto.val("");
            if ($inputLitragem.val() === "0") $inputLitragem.val("");
            if ($inputValor.val() === "0") $inputValor.val("");

            // Torna os campos obrigatórios
            $inputProduto.prop('required', true);
            $inputLitragem.prop('required', true);
            $inputValor.prop('required', true);
        } else {
            // Se NÃO: Esconde os campos com efeito suave
            $containerArla.fadeOut();
            
            // Preenche automaticamente com "0" para não quebrar o backend
            $inputProduto.val("0");
            $inputLitragem.val("0");
            $inputValor.val("0");

            // Remove o 'required' para o HTML5 permitir o envio do formulário
            $inputProduto.prop('required', false);
            $inputLitragem.prop('required', false);
            $inputValor.prop('required', false);
        }
    });

    // Aplica a máscara decimal que você já tem criada no projeto para os novos campos de valor/litragem do ARLA
    if (typeof aplicarMascaraDecimal === 'function') {
        aplicarMascaraDecimal('litragem_arla');
        aplicarMascaraDecimal('valor_arla');
    }
});
</script>



@endsection
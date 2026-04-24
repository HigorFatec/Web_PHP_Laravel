@extends('layout')
@section('title', 'Despesas')
@section('conteudo')


@php $user = auth()->user(); @endphp

@if (@auth()->user()->id != null)

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

<div class="card-panel blue lighten-5 z-depth-2" style="border-radius: 10px; border: 2px solid #1976d2;">
    <div class="row" style="margin-bottom: 0; display: flex; align-items: center; flex-wrap: wrap;">
        
        @if(isset($adiantamentoModel) && $adiantamentoModel->valor > 0)
            <div class="col s12 m4 center-align">
                <span class="white-text text-darken-2" style="font-size: 0.8rem; text-transform: uppercase;">Adiantamento</span>
                <h5 class="blue-text text-darken-2" style="margin: 0; font-weight: bold;">
                    R$ <span id="display-adiantamento">{{ number_format($adiantamentoModel->valor, 2, ',', '.') }}</span>
                </h5>
            </div>
            <div class="col s12 m4 center-align" style="border-left: 1px solid #bbdefb; border-right: 1px solid #bbdefb;">
                <span class="white-text text-darken-2" style="font-size: 0.8rem; text-transform: uppercase;">Total em Notas</span>
                <h5 class="orange-text text-darken-3" style="margin: 0; font-weight: bold;">
                    R$ <span id="display-total-notas">0,00</span>
                </h5>
            </div>
            <div class="col s12 m4 center-align">
                <span class="white-text text-darken-2" style="font-size: 0.8rem; text-transform: uppercase;">Saldo Final</span>
                <h4 class="green-text text-darken-2" style="margin: 0; font-weight: bold;">
                    R$ <span id="display-saldo-final">{{ number_format($adiantamentoModel->valor, 2, ',', '.') }}</span>
                </h4>
            </div>
        @else
            <div class="col s12 center-align">
                <span class="white-text text-darken-2" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Total das Despesas Adicionadas</span>
                <h3 class="blue-text text-darken-3" style="margin: 5px 0; font-weight: bold;">
                    R$ <span id="display-total-geral">0,00</span>
                </h3>
            </div>
        @endif

    </div>
</div>

        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Cadastro de Despesas</b></span>




<form action="{{ route('despesa.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <span class="card-title center"><b>Dados do Solicitante</b></span><br>
    <input type="text" name="user_name" value="{{ $user->name }}" readonly>
    <input type="email" name="user_email" value="{{ $user->email }}" readonly><br>

    <span class="card-title center"><b>Dados do Funcionário</b></span>
    <center><span class="card-description">Obs:<b>No rodopar</b> precisa estar cadastrado como <b>funcionário</b></span></center>
    <input type="text" id="search_fornecedor" placeholder="Buscar funcionário...">
    <select name="fornecedor" id="fornecedor_select" class="browser-default" required>
        <option value="">Selecione...</option>
    </select><br><br>

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

    <div id="container-despesas">
        <div class="despesa-item card-panel grey lighten-5" style="position: relative; padding-top: 20px;">
            <span class="blue-text"><b>Despesa #1</b></span>
            <hr>
            
            <div class="row">
                <div class="col s12 m6">
                    Tipo de Despesa:
                    <select name="despesa[]" class="browser-default" required>
                        <option value=""></option>
                        @foreach ($produtos as $c)
                            <option value="{{ $c->CODPROD }}">{{ $c->DESCRI }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col s12 m6">
                    Data:
                    <input type="date" name="date[]" required>
                </div>
                <div class="col s12 m6">
                    Valor:
                    <input type="text" name="valor[]" class="valor-mask" required>
                </div>
                <div class="col s12 m6">
                    Nota Fiscal/Recibo:
                    <div class="drop-zone" style="border: 2px dashed #1976d2; padding: 20px; text-align: center; border-radius: 5px; background: #e3f2fd; cursor: pointer;">
                        <span class="drop-zone__prompt">Arraste a foto aqui ou clique para selecionar</span>
                        <input type="file" name="foto[]" class="drop-zone__input" accept="image/*" required style="display: none;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <center>
        <button type="button" id="btn-add-despesa" class="btn blue darken-2">
            <i class="material-icons left">add</i> Adicionar mais despesa
        </button>
    </center>
    <br>

    <center>
        <button class="btn green btn-large" type="submit">
            Salvar Tudo <i class="material-icons right">send</i>
        </button>
    </center>
</form>
</body>
</html>

@else
<script>
    alert('Você precisa estar logado para acessar essa página!');
    window.location.href = '/login';
</script>
@endif


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
$(document).ready(function() {
    let contador = 1;

    $('#btn-add-despesa').click(function() {
        contador++;
        
        // Clona a primeira div de despesa
        let novoItem = $('.despesa-item').first().clone();
        
        // 1. Limpa os valores dos inputs e selects
        novoItem.find('input').val('');
        novoItem.find('select').val('');
        
        // 2. Reseta o texto da Drop Zone para o padrão
        novoItem.find('.drop-zone__prompt').text("Arraste a foto aqui ou clique para selecionar");
        
        // 3. Reseta o estilo da borda e fundo da Drop Zone
        novoItem.find('.drop-zone').css({
            'border-style': 'dashed',
            'background': '#e3f2fd'
        });

        // Atualiza o título do número da despesa
        novoItem.find('.blue-text b').text('Despesa #' + contador);
        
        // Adiciona o botão de remover (se já não tiver)
        if (novoItem.find('.remove-despesa').length === 0) {
            novoItem.append('<a href="javascript:void(0)" class="remove-despesa red-text" style="position:absolute; top:10px; right:10px;"><i class="material-icons">delete</i></a>');
        }
        
        // Adiciona ao container
        $('#container-despesas').append(novoItem);
    });

    // Função para remover uma linha
    $(document).on('click', '.remove-despesa', function() {
        $(this).closest('.despesa-item').remove();
    });

// 1. Restringe a digitação apenas a caracteres válidos
$(document).on('input', '.valor-mask', function() {
    this.value = this.value.replace(/[^0-9,.]/g, "");
});

// 2. Formata para duas casas decimais ao perder o foco
$(document).on('blur', '.valor-mask', function() {
    let v = this.value.replace(",", ".");
    let floatVal = parseFloat(v);
    if (!isNaN(floatVal)) {
        this.value = floatVal.toFixed(2);
    }
});
});
</script>

<script>
$(document).ready(function() {
    // Pegamos o valor do adiantamento (0 se não existir)
    const valorAdiantamentoOriginal = {{ $adiantamentoModel->valor ?? 0 }};

    function calcularTotais() {
        let totalNotas = 0;

        $('.valor-mask').each(function() {
            let valorRaw = $(this).val();
            if(!valorRaw) return;

            // LÓGICA DE LIMPEZA À PROVA DE ERROS:
            // 1. Se o valor já tiver vírgula (formato BR), removemos o ponto de milhar e trocamos a vírgula por ponto.
            // 2. Se o valor NÃO tiver vírgula mas tiver ponto, verificamos se é decimal ou milhar.
            
            let valorLimpo = valorRaw.trim();
            
            if (valorLimpo.includes(',')) {
                // Formato: 1.250,50 -> 1250.50
                valorLimpo = valorLimpo.replace(/\./g, '').replace(',', '.');
            }
            // Se não tem vírgula, o parseFloat padrão do JS já entende o ponto como decimal.

            let floatVal = parseFloat(valorLimpo);
            
            if (!isNaN(floatVal)) {
                totalNotas += floatVal;
            }
        });

        // Formatação para exibição no padrão Brasileiro
        let totalFormatado = totalNotas.toLocaleString('pt-br', {
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        });

        // Atualiza os displays
        $('#display-total-notas').text(totalFormatado);
        $('#display-total-geral').text(totalFormatado);

        // Lógica de Saldo (caso HAJA adiantamento)
        if (valorAdiantamentoOriginal > 0) {
            let saldoFinal = valorAdiantamentoOriginal - totalNotas;
            
            $('#display-saldo-final').text(saldoFinal.toLocaleString('pt-br', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Troca de cores baseada no saldo
            if(saldoFinal < 0) {
                $('#display-saldo-final').addClass('red-text').removeClass('green-text');
            } else {
                $('#display-saldo-final').addClass('green-text').removeClass('red-text');
            }
        }
    }

    // --- EVENTOS ---

    // Calcula enquanto digita
    $(document).on('input', '.valor-mask', calcularTotais);
    
    // Recalcula ao sair do campo (garante após a formatação do blur)
    $(document).on('blur', '.valor-mask', function() {
        setTimeout(calcularTotais, 50);
    });

    // Quando remover uma despesa
    $(document).on('click', '.remove-despesa', function() {
        setTimeout(calcularTotais, 100);
    });

    // Quando adicionar uma despesa
    $('#btn-add-despesa').click(function() {
        setTimeout(calcularTotais, 150);
    });
});
</script>


<script>
$(document).ready(function() {
    // 1. Ao clicar na zona, dispara o clique no input escondido
    // Usamos $(document).on para garantir que funcione em campos adicionados dinamicamente
    $(document).on('click', '.drop-zone', function(e) {
        // Evita que o clique entre em loop infinito
        if (!$(e.target).hasClass('drop-zone__input')) {
            $(this).find('.drop-zone__input').click();
        }
    });

    // 2. Atualiza o texto quando o arquivo é selecionado (via clique ou arraste)
    $(document).on('change', '.drop-zone__input', function() {
        if (this.files && this.files.length) {
            let fileName = this.files[0].name;
            // Busca o span de texto dentro da mesma drop-zone
            $(this).closest('.drop-zone').find('.drop-zone__prompt').text("Arquivo: " + fileName);
            $(this).closest('.drop-zone').css('border-style', 'solid');
        }
    });

    // Lógica de Drag & Drop
    $(document).on('dragover', '.drop-zone', function(e) {
        e.preventDefault();
        $(this).css('background', '#bbdefb');
    });

    $(document).on('dragleave', '.drop-zone', function(e) {
        e.preventDefault();
        $(this).css('background', '#e3f2fd');
    });

    $(document).on('drop', '.drop-zone', function(e) {
        e.preventDefault();
        $(this).css('background', '#e3f2fd');
        
        if (e.originalEvent.dataTransfer.files.length) {
            let input = $(this).find('.drop-zone__input')[0];
            input.files = e.originalEvent.dataTransfer.files;
            $(input).trigger('change');
        }
    });
});
</script>

@endsection



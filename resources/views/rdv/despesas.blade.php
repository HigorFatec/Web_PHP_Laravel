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

        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Cadastro de Despesas</b></span>

      <form action="{{ route('despesa.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
        @csrf

        <span class="card-title center"><b>Dados do Solicitante</b></span><br>
        <input type="text" name="user_name" placeholder="Nome Completo" required value = "{{ $user->name ?? ''}}" readonly>
        <input type="email" name="user_email" placeholder="E-mail" required value = "{{ $user->email ?? ''}}" readonly><br>




        <span class="card-title center"><b>Dados do Funcionário</b></span>
            <center><span>Não encontrou o funcionário? <a href="https://suporte.grupocargopolo.com.br:13004/WOListView.do">Clique aqui para abrir um chamado para cadastro</a></span></center>

            Funcionário: <br>
            <input type="text" id="search_fornecedor" placeholder="Buscar funcionário... (Razão Social, Codigo Rodopar, CPF/CNPJ RESPEITANDO A PONTUAÇÃO !!!! )">

            <select name="fornecedor" id="fornecedor_select" class="browser-default" required>
                <option value="">Selecione...</option>
        </select><br><br>

        <span class="card-title center"><b>Selecione o tipo de Despesa:</b></span>

        Despesa:
            <select name="despesa" id="despesa_select" class="browser-default" required>
              <option value = ""></option>
                @foreach ($produtos as $c)
                    <option value="{{ $c->CODPROD }}">{{ $c->DESCRI }}</option>
                @endforeach
            </select><br>

        Ocorrência da Despesa:
        <input type="date" name="date" placeholder="Ocorrência da Despesa" required><br><br>

        Valor:
        <input type="text" id="valor" name="valor" required><br><br>

        Nota Fiscal/Recibo:<br>
        <input type="file" name="foto" id="foto" accept="image/*"><br><br>





        </div>

        <center><button class="btn" type="submit" name="action">Enviar
            <i class="material-icons right">send</i>
          </button></center><br>


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
                            data-rg="${f.rg ?? ''}">
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

@endsection



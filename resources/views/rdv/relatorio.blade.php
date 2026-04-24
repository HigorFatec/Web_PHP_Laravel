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
                <span class="card-title center"><b>Novo relatório</b></span><br>
                        <span class="card-title center"><b>Detalhes do Relatório</b></span>


      <form action="{{ route('relatorio.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
        @csrf

        Titulo:
        <input type="text" name='titulo' required>

        Motivo: 
        <input type="text" name="motivo" required>



        <br>
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
        </select><br><br>

                        <span class="card-title center"><b>Detalhes da Viagem</b></span>

        Data de Início da Viagem:
        <input type="date" name="inicio_viagem">

        Data de Fim da Viagem:
        <input type="date" name="fim_viagem"><br><br>

                                <span class="card-title center"><b>Custo da viagem</b></span>

        

<div class="row">
    <div class="col s12">
        <table class="striped highlight responsive-table">
            <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" id="select-all" />
                            <span>Incluir</span>
                        </label>
                    </th>
                    <th>Data</th>
                    <th>Funcionário</th>
                    <th>Despesa</th>
                    <th>Valor</th>
                    <th>Anexo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($despesas as $despesa)

                @php
                    $url = asset('storage/' . $despesa->anexo);
                @endphp
                
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="despesas_selecionadas[]" class="checkbox-despesa" value="{{ $despesa->id }}" />
                                <span></span>
                            </label>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($despesa->date)->format('d/m/Y') }}</td>
                        <td>{{ substr($despesa->descricao_fornecedor,0,10) }}</td>
                        <td>{{ $despesa->descricao_despesa }}</td>
                        <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                        <td><a href="{{ $url }}" target="_blank">Visualizar</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="center-align" style="padding: 20px;">
                            <i>Nenhuma despesa pendente para adicionar ao relatório.</i>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>






        
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


<script>
    $(document).ready(function() {
    // Evento para o checkbox "Selecionar Todos"
    $('#select-all').click(function() {
        // Define o estado de todos os checkboxes baseados no "select-all"
        $('.checkbox-despesa').prop('checked', this.checked);
        
        // Opcional: Adiciona uma cor de destaque na linha (tr) se estiver marcado
        atualizarDestaqueLinhas();
    });

    // Evento para checkboxes individuais
    $(document).on('change', '.checkbox-despesa', function() {
        // Se algum for desmarcado, o "Selecionar Todos" também desmarca
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        }
        
        // Se todos forem marcados manualmente, o "Selecionar Todos" marca sozinho
        if ($('.checkbox-despesa:checked').length == $('.checkbox-despesa').length) {
            $('#select-all').prop('checked', true);
        }
        
        atualizarDestaqueLinhas();
    });

    function atualizarDestaqueLinhas() {
        $('.checkbox-despesa').each(function() {
            if ($(this).is(':checked')) {
                $(this).closest('tr').css('background-color', '#e3f2fd'); // Azul claro
            } else {
                $(this).closest('tr').css('background-color', '');
            }
        });
    }
});


// Exemplo de verificação antes de enviar
$('form').on('submit', function(e) {
    if ($('.checkbox-despesa:checked').length === 0) {
        e.preventDefault();
        swal("Atenção!", "Selecione pelo menos uma despesa para criar o relatório.", "warning");
        return false;
    }
});

</script>

@endsection
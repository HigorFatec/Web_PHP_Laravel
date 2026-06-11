@extends('layout')
@section('title', 'Implantação de Saldo')
@section('conteudo')

<div class="row">

@if (@auth()->user()->id != null)

<div class="col s12 m6 offset-m3">
<div class="card">
    <div class="card-content">
    {{-- for de 1 a 5 --}}
    @for ($i = 1; $i <= 5; $i++)
        @if ($message = Session::get('success'.$i))
            <div class="card green darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Sucesso!</span>
                    <p>Parabéns! A solicitação foi realizada com sucesso!<br></p>
                </div>
            </div>
        @endif
    @endfor

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <div class="card red darken-1">
              <div class="card-content white-text">
                <span class="card-title">Erro</span>
                <p>Corrija os seguintes erros para prosseguir:<br>{{$error}}</p>
              </div>
            </div>
          @endforeach
        </ul>
    </div>
    @endif
</div>
</div>

<div class="row">
    <div class="col s12 m12">
      <div class="card gray">
        <div class="card-content">
          <span class="card-title font-weight-bold" style="color: black">Ajuste de Saldo</span>
          <br>
          
          <form action="{{ route('implantacao_saldo.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="input-field col s12 m6">
                    <select name="cod_localizacao" id="unidade" required>
                        <option value="" disabled selected>Escolha a Filial</option>
                        @foreach ($filiais as $f)
                            <option value="{{ $f->CODIGO }}">{{ $f->DESCRI }}</option>
                        @endforeach
                    </select>
                    <label>Unidade Filial</label>
                </div>

                <div class="input-field col s12 m6">
                    <select name="movimentacao" required>
                        <option value="" disabled selected>Escolha a Movimentação</option>
                        <option value="Entrada">Entrada (Ajuste Positivo)</option>
                        <option value="Saída">Saída (Ajuste Negativo)</option>
                    </select>
                    <label>Tipo de Movimentação</label>
                </div>
            </div>

            <div class="row border-box" style="background: #fcfcfc; padding: 15px; border: 1px dashed #ccc; border-radius: 8px; margin-bottom: 20px;">
                <h6 style="margin-top: 0; font-weight: bold; color: #333;">Buscar e Adicionar Itens ao Lote</h6>
                
                <div class="input-field col s12 m6">
                    <select id="produto_select" class="browser-default select2" style="width: 100%; height: 45px; border: 1px solid #9e9e9e; border-radius: 4px;">
                        <option value="" disabled selected>Digite o código ou nome do produto...</option>
                        @foreach ($produtos as $p)
                            <option value="{{ $p->CODPROD }}" 
                                    data-descri="{{ $p->DESCRI }}" 
                                    data-grupo="{{ $p->CODGPP }}" 
                                    data-subgrupo="{{ $p->CODSGP }}">
                                {{ $p->CODPROD }} - {{ $p->DESCRI }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="input-field col s12 m4">
                    <input type="number" id="quantidade_input" min="1" placeholder="Quantidade">
                </div>

                <div class="col s12 m2" style="margin-top: 15px;">
                    <button type="button" id="btn-adicionar-produto" class="btn blue waves-effect waves-light style-flex">
                        <i class="material-icons">add</i> Adicionar
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <h6 class="font-weight-bold">Produtos Selecionados para este Lote:</h6>
                    <table class="striped table-responsive">
                        <thead>
                            <tr>
                                <th width="60" style="text-align: center;">Item</th>
                                <th width="140">Código</th>
                                <th>Descrição do Produto</th>
                                <th width="120">Quantidade</th>
                                <th width="80">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-produtos-corpo">
                            @if(isset($itensTemporarios) && count($itensTemporarios) > 0)
                                @foreach($itensTemporarios as $index => $item)
                                    <tr data-codprod="{{ $item->codprod }}">
                                        <td style="text-align: center; vertical-align: middle;">
                                            <span class="badge-item-numero">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <span class="chip text-weight-bold grey lighten-2" style="border-radius: 4px; font-weight: bold;">{{ $item->codprod }}</span>
                                            <input type="hidden" name="produtos[{{ $item->codprod }}][codprod]" value="{{ $item->codprod }}">
                                        </td>
                                        <td style="font-size: 14px; color: #212121;">{{ $item->descricao }}</td>
                                        <td>
                                            <input type="number" name="produtos[{{ $item->codprod }}][quantidade]" value="{{ $item->quantidade }}" min="1" class="browser-default" style="width: 80px; text-align: center; border: 1px solid #ccc; border-radius: 4px; padding: 5px;">
                                        </td>
                                        <td>
                                            <button type="button" class="btn-remove-item btn-floating btn-small red waves-effect waves-light" data-codprod="{{ $item->codprod }}">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="input-field col s12 m12">
                    <input type="text" name="observacao" required>
                    <label>Observação / Justificativa Geral do Lote</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m4">
                    <select name="gestor_filial" id="gestor_filial" required>
                        <option value="" disabled selected>Escolha o Gestor Local...</option>
                        @foreach ($local as $l)
                            <option value="{{ $l->email }}" data-setor="{{ $l->setor }}">{{ $l->nome }}</option>
                        @endforeach
                    </select>
                    <label>Aprovação Local (Filial)</label>
                </div>

                <div class="input-field col s12 m4">
                    <select name="gestor_regional" id="gestor_regional" required>
                        <option value="" disabled selected>Escolha o Regional...</option>
                        @foreach ($regional as $r)
                            <option value="{{ $r->email }}" data-setor="{{ $r->setor }}">{{ $r->nome }}</option>
                        @endforeach
                    </select>
                    <label>Aprovação Regional</label>
                </div>

                <div class="input-field col s12 m4">
                    <select name="diretor" id="diretor" required>
                        <option value="" disabled selected>Escolha a Diretoria...</option>
                        @foreach ($diretor as $d)
                            <option value="{{ $d->email }}" data-setor="{{ $d->setor }}">{{ $d->nome }}</option>
                        @endforeach
                    </select>
                    <label>Aprovação Diretoria (Diretor)</label>
                </div>
            </div>

            <div class="row center-align">
                <button type="submit" class="btn green waves-effect waves-light large">
                    <i class="material-icons left">send</i> Enviar Lote para Aprovação
                </button>
            </div>

          </form>

        </div>
      </div>
    </div>
</div>

@else
<div class="card red darken-1">
    <div class="card-content white-text">
      <span class="card-title">Erro de Autenticação</span>
      <p>Precisa de estar logado para aceder a esta página.</p>
    </div>
</div>
@endif

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    if ($.fn.formSelect) {
        $('select').not('#produto_select').formSelect();
    }

    if ($.fn.select2) {
        $('#produto_select').select2({
            placeholder: "Digite o código ou nome do produto...",
            allowClear: true
        });
    }

    let contadorItem = $('#tabela-produtos-corpo tr').length + 1;

    $('#btn-adicionar-produto').on('click', function(e) {
        e.preventDefault();

        const $opcao = $('#produto_select option:selected');
        const codprod = $('#produto_select').val();
        const descri = $opcao.data('descri');
        const grupo = $opcao.data('grupo');
        const subgrupo = $opcao.data('subgrupo');
        const qtd = $('#quantidade_input').val();

        if (!codprod) { alert('Por favor, selecione um produto.'); return; }
        if (!qtd || qtd <= 0) { alert('Insira uma quantidade válida superior a 0.'); return; }

        if ($(`tr[data-codprod="${codprod}"]`).length > 0) {
            alert('Este produto já foi adicionado a este lote.');
            return;
        }

        $.ajax({
            url: "{{ route('implantacao_saldo.add_temp') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                codprod: codprod,
                descricao: descri,
                grupo: grupo,
                subgrupo: subgrupo,
                quantidade: qtd
            },
            success: function(response) {
                if (response.success) {
                    const badgeIndicador = `<span class="badge-item-numero">${contadorItem}</span>`;
                    const novaLinha = `
                        <tr data-codprod="${codprod}">
                            <td style="text-align: center; vertical-align: middle;">${badgeIndicador}</td>
                            <td>
                                <span class="chip text-weight-bold grey lighten-2" style="border-radius: 4px; font-weight: bold;">${codprod}</span>
                                <input type="hidden" name="produtos[${codprod}][codprod]" value="${codprod}">
                            </td>
                            <td style="font-size: 14px; color: #212121;">${descri}</td>
                            <td>
                                <input type="number" name="produtos[${codprod}][quantidade]" value="${qtd}" min="1" class="browser-default" style="width: 80px; text-align: center; border: 1px solid #ccc; border-radius: 4px; padding: 5px;">
                            </td>
                            <td>
                                <button type="button" class="btn-remove-item btn-floating btn-small red waves-effect waves-light" data-codprod="${codprod}">
                                    <i class="material-icons">delete</i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#tabela-produtos-corpo').append(novaLinha);
                    contadorItem++;

                    $('#produto_select').val('').trigger('change');
                    $('#quantidade_input').val('');
                } else {
                    alert('Erro ao tentar processar o rascunho do item.');
                }
            },
            error: function() {
                alert('Erro de comunicação: Não foi possível salvar o item no banco de dados temporário.');
            }
        });
    });

    $(document).on('click', '.btn-remove-item', function() {
        const $linha = $(this).closest('tr');
        const codprod = $(this).data('codprod');

        if (confirm('Deseja realmente remover este item do rascunho?')) {
            $.ajax({
                url: "{{ route('implantacao_saldo.remove_temp') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    codprod: codprod
                },
                success: function(response) {
                    if (response.success) {
                        $linha.remove();
                        contadorItem = 1;
                        $('#tabela-produtos-corpo tr').each(function() {
                            $(this).find('.badge-item-numero').text(contadorItem);
                            contadorItem++;
                        });
                    }
                },
                error: function() {
                    alert('Erro ao tentar remover o item do banco de dados.');
                }
            });
        }
    });

    // 🌟 NOVO SISTEMA DE FILTRO DE GESTORES BLINDADO (NÃO BLOQUEIA SE FALHAR)

    $('#unidade').on('change', function() {
        const unidadeCodigo = String($(this).val()).trim();
        const unidadeTexto = $("#unidade option:selected").text().trim().toUpperCase();
        const $gestores = $('#gestor_filial, #gestor_regional, #diretor');

        if (!unidadeCodigo) {
            $gestores.val('');
            if ($.fn.formSelect) $gestores.formSelect();
            return;
        }

        function normalizar(txt) {
            if (!txt) return '';
            return String(txt).normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toUpperCase();
        }

        const textoFilialNormalizado = normalizar(unidadeTexto);

        $gestores.each(function() {
            const $selectGestor = $(this);
            let encontrado = false;

            $selectGestor.find('option').each(function() {
                const setorGestor = $(this).attr('data-setor');
                if (setorGestor) {
                    const setorNormalizado = normalizar(setorGestor);

                    // Divide as palavras para testar correspondências parciais por aproximação
                    const palavrasChave = setorNormalizado.split(' ').filter(p => p.length > 2);
                    let bateuPalavra = false;
                    
                    palavrasChave.forEach(palavra => {
                        if (textoFilialNormalizado.includes(palavra)) {
                            bateuPalavra = true;
                        }
                    });

                    if (
                        unidadeCodigo === setorNormalizado || 
                        textoFilialNormalizado.includes(setorNormalizado) || 
                        setorNormalizado.includes(textoFilialNormalizado) ||
                        bateuPalavra
                    ) {
                        $selectGestor.val($(this).val());
                        $selectGestor.prop('readonly', true);
                        $gestores.formSelect();
                        encontrado = true;
                        return false; 
                    }
                }
            });

            // Se a automação falhar, mantém a opção padrão vazia para o usuário escolher manualmente
            if (!encontrado) {
                $selectGestor.val('');
            }
        });

        // Atualiza os componentes visuais do Materialize
        if ($.fn.formSelect) {
            $gestores.formSelect();
        }
    });
});
</script>

<style>
.style-flex { display: inline-flex; align-items: center; justify-content: center; gap: 5px; }
.table-responsive { width: 100%; margin-top: 10px; }
.select2-container--default .select2-selection--single {
    height: 45px !important;
    border: 1px solid #9e9e9e !important;
    border-radius: 4px !important;
    padding-top: 8px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 43px !important;
}
.badge-item-numero {
    background: #455a64;
    color: white;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}
</style>

@endsection
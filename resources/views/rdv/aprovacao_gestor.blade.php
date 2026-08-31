@extends('layout')
@section('title','Análise Detalhada de Despesas')
@section('conteudo')

<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $existePendente = $despesas->contains('status', 'pendente');
    $existeReprovado = $despesas->contains('status', 'reprovado');
    // Verifica se TODAS as despesas possuem o status 'aprovado'
    $tudoAprovado = $despesas->every('status', 'aprovado');
@endphp

@if (session('erro_pix'))
    <div class="row">
        <div class="col s12 m10 offset-m1">
            <div class="card orange darken-2">
                <div class="card-content white-text">
                    <span class="card-title"><i class="material-icons left">warning</i> Ação Necessária</span>
                    <p>{{ session('erro_pix') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif


{{-- Alerta de Sucesso (Caso queira aproveitar) --}}
@if (session('success'))
    <div class="row">
        <div class="col s12 m8 offset-m2">
        <div class="card green darken-1">
            <div class="card-content white-text">
                <span class="card-title">Sucesso!</span>
                <p>{{ session('success') }}</p>
            </div>
        </div>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="row">
        <div class="col s12 m10 offset-m1">
            @foreach ($errors->all() as $error)
                <div class="card red darken-1">
                    <div class="card-content white-text">
                        <span class="card-title"><i class="material-icons left">error</i> Atenção</span>
                        <p>{{ $error }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="container">
    {{-- Painel de Resumo Financeiro (Atualiza a cada clique) --}}
    <div class="row" style="margin-top: 20px;">
        <div class="col s12">
            <div class="card-panel z-depth-2 grey lighten-4" style="border-radius: 8px;">
                <div class="row" style="margin-bottom: 0; display: flex; align-items: center; flex-wrap: wrap;">
                    <div class="col s12 m5">
                        <h5 style="margin: 0; font-weight: bold;">{{ $relatorio->titulo }}</h5>
                        <p class="grey-text text-darken-1" style="margin: 5px 0 0 0;">
                            Solicitante: <b>{{ $relatorio->user_name }}</b>
                        </p>
                    </div>
                    
                    <div class="col s6 m3 center-align">
                        <span class="grey-text" style="font-size: 0.7rem; uppercase">VALOR SOLICITADO</span>
                        <h6 class="grey-text" style="text-decoration: line-through; margin: 0;">
                            R$ {{ number_format($relatorio->valor, 2, ',', '.') }}
                        </h6>
                    </div>
                    
                    <div class="col s6 m4 center-align" style="border-left: 1px solid #ddd;">
                        <span class="green-text text-darken-2" style="font-size: 0.8rem; font-weight: bold;">VALOR ATUAL APROVADO</span>
                        <h4 class="green-text text-darken-2" style="margin: 0; font-weight: bold;">
                            R$ {{ number_format($valorAprovado, 2, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dados de Alocação --}}
    <div class="row">
        <div class="col s12">
            <div class="card-panel white" style="padding: 15px; margin-top: 0;">
                <div class="row" style="margin-bottom: 0;">
                    <div class="col s12 m4">
                        <b>Unidade:</b> {{ $relatorio->unidades->unidade_negocio ?? 'N/A' }}
                    </div>
                    <div class="col s12 m4">
                        <b>C. Custo:</b> {{ $relatorio->centroCusto->descri_custo ?? 'N/A' }}
                    </div>
                    <div class="col s12 m4">
                        <b>C. Gasto:</b> {{ $relatorio->centroGasto->descri_gasto ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5>Itens para Conferência</h5>
    <div class="row">
        @foreach($despesas as $despesa)
        <div class="col s12">
            <div class="card horizontal" style="border-left: 10px solid 
                @if($despesa->status == 'aprovado') #4caf50 
                @elseif($despesa->status == 'reprovado') #f44336 
                @else #9e9e9e @endif; border-radius: 0 8px 8px 0;">
                
                {{-- <div class="card-image" style="padding: 15px; display: flex; align-items: center; background: #fafafa;">
                    <img src="{{ asset('storage/' . $despesa->anexo) }}" class="materialboxed" 
                         style="width: 140px; height: 140px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                </div> --}}
                <div class="card-image" style="padding: 15px; display: flex; align-items: center; background: #fafafa; min-height: 170px; justify-content: center;">
                    @php
                        $extensao = pathinfo($despesa->anexo, PATHINFO_EXTENSION);
                        $url = asset('storage/' . $despesa->anexo);
                    @endphp

                    @if(in_array(strtolower($extensao), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ $url }}" class="materialboxed" 
                            style="width: 140px; height: 140px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    
                    @elseif(strtolower($extensao) === 'pdf')
                        <div style="text-align: center;">
                            <a href="{{ $url }}" target="_blank" style="text-decoration: none; color: #d32f2f;">
                                <i class="material-icons" style="font-size: 5rem;">picture_as_pdf</i>
                                <span style="display: block; font-size: 12px; font-weight: bold;">VER PDF</span>
                            </a>
                        </div>
                    @else
                        <a href="{{ $url }}" target="_blank">Baixar Arquivo</a>
                    @endif
                </div>



                
                <div class="card-stacked">
                    <div class="card-content">
                        <div class="row" style="margin-bottom: 0;">
                            <div class="col s8">
                                <span class="card-title"><b>{{ $despesa->descricao_despesa }}</b></span>
                                <p><b>Fornecedor:</b> {{ $despesa->descricao_fornecedor }}</p>
                                <p><b>Data:</b> {{ date('d/m/Y', strtotime($despesa->date ?? now())) }}</p>
                            </div>
                            <div class="col s4 right-align">
                                <h5 class="green-text text-darken-2" style="margin: 0;">R$ {{ number_format($despesa->valor, 2, ',', '.') }}</h5>
                                <span class="badge white-text {{ $despesa->status == 'aprovado' ? 'green' : ($despesa->status == 'reprovado' ? 'red' : 'orange') }}" style="border-radius: 4px; float: right; position: relative;">
                                    {{ strtoupper($despesa->status) }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('despesa.status', $despesa->id) }}" method="POST" class="form-aprovacao">
                            @csrf
                            <div class="row" style="margin-top: 15px; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee;">
                                <div class="input-field col s12 m7">
                                    <i class="material-icons prefix" style="font-size: 1.2rem;">comment</i>
                                    <textarea name="observacao" class="materialize-textarea" placeholder="Justificativa (obrigatória para reprovação)">{{ $despesa->observacao }}</textarea>
                                </div>
                                <div class="col s12 m5 center-align" style="padding-top: 10px;">
                                    {{-- Botões com efeito visual de seleção --}}
                                    <button name="status" value="aprovado" 
                                            class="btn waves-effect {{ $despesa->status == 'aprovado' ? 'green' : 'white green-text' }}" 
                                            style="width: 120px; border: 2px solid #4caf50; font-weight: bold;">
                                        APROVAR
                                    </button>
                                    <button name="status" value="reprovado" 
                                            class="btn waves-effect {{ $despesa->status == 'reprovado' ? 'red' : 'white red-text' }}" 
                                            style="width: 120px; border: 2px solid #f44336; font-weight: bold;">
                                        REPROVAR
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

   {{-- Rodapé de Finalização --}}
<div class="row">
    <div class="col s12 center-align" style="margin: 40px 0;">
        
        @if($existePendente)
            {{-- CASO 1: Ainda existem itens sem análise --}}
            <div class="card-panel grey lighten-4" style="border-radius: 12px;">
                <h6 class="orange-text text-darken-2"><i class="material-icons left">info_outline</i> ITENS PENDENTES DE ANÁLISE</h6>
                <p class="grey-text">Você precisa analisar todos os recibos acima para habilitar as ações de finalização.</p>
            </div>

        @elseif($tudoAprovado)
            {{-- CASO 2: Tudo aprovado (Habilita botão de Aprovação do Relatório) --}}
            <div class="card-panel green lighten-5" style="border: 2px dashed #2e7d32; border-radius: 12px;">
                <h5 class="green-text text-darken-3"><b>Tudo em ordem!</b></h5>
                <p>Todas as despesas foram conferidas e aprovadas.</p>
                
                <form action="{{ route('relatorio.finalizar', $relatorio->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status_final" value="aprovado">
                    <button type="submit" class="btn-large green darken-2 pulse" style="border-radius: 30px; padding: 0 40px;">
                        <i class="material-icons left">check_circle</i> APROVAR RELATÓRIO E ENVIAR AO FINANCEIRO
                    </button>
                </form>
            </div>

        @else
            {{-- CASO 3: Não há pendentes, mas existe pelo menos uma reprovada --}}
            <div class="card-panel red lighten-5" style="border: 2px dashed #d32f2f; border-radius: 12px;">
                <h5 class="red-text text-darken-3"><b>Relatório com Restrições</b></h5>
                <p>Existem itens **reprovados** neste relatório. Por política de segurança, este relatório deve ser reprovado para correção.</p>
                
                <form action="{{ route('relatorio.finalizar', $relatorio->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status_final" value="reprovado">
                    <button type="submit" class="btn-large red darken-2" style="border-radius: 30px; padding: 0 40px;">
                        <i class="material-icons left">cancel</i> REPROVAR RELATÓRIO INTEGRALMENTE
                    </button>
                </form>
            </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Materialbox.init(document.querySelectorAll('.materialboxed'));
        M.Textarea.init(document.querySelectorAll('.materialize-textarea'));
    });
</script>

<style>
    .card.horizontal .card-image { max-width: 180px; }
    .input-field textarea { font-size: 0.9rem; }
    .card-title { font-size: 1.1rem !important; margin-bottom: 5px !important; }
    body { background-color: #f0f2f5; }
</style>


<script>
document.querySelectorAll('.form-aprovacao').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); 

        const formData = new FormData(this);
        const statusClicado = e.submitter.value; 
        formData.append('status', statusClicado);

        const url = this.action;
        const card = this.closest('.card'); 

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 1. Atualiza o Badge de Status
                const badge = card.querySelector('.badge');
                badge.innerText = data.novo_status.toUpperCase();
                
                // 2. Atualiza as cores do Card e botões
                if (data.novo_status === 'aprovado') {
                    card.style.borderLeft = "10px solid #4caf50";
                    badge.className = "badge white-text green";
                } else {
                    card.style.borderLeft = "10px solid #f44336";
                    badge.className = "badge white-text red";
                }

                M.toast({html: data.message, classes: 'rounded green'});

                // --- O PONTO CHAVE: Chama a função para verificar o rodapé ---
                atualizarEstadoDoRodape();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            M.toast({html: 'Erro ao processar', classes: 'rounded red'});
        });
    });
});

function atualizarEstadoDoRodape() {
    // 1. Pega todos os status atuais da tela
    const todosStatus = Array.from(document.querySelectorAll('.card-stacked .badge'))
                             .map(b => b.innerText.toLowerCase().trim());

    const temPendente = todosStatus.includes('pendente');

    // 2. Se não houver mais nenhum pendente, precisamos dar o refresh
    // para que o Laravel reconstrua o rodapé (exibindo o botão verde ou vermelho de finalização)
    // e atualize o cálculo do Valor Total Aprovado no topo.
    if (!temPendente) {
        M.toast({html: 'Análise concluída! Atualizando opções finalização...', classes: 'rounded blue'});
        
        // Pequeno delay para o usuário ver que o último item mudou de cor antes de atualizar
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
}
</script>

@endsection
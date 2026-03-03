@extends('layout')
@section('title','Análise Detalhada de Despesas')
@section('conteudo')

@php
    $existePendente = $despesas->contains('status', 'pendente');
@endphp

  @if ($errors->any())
  
<div class="row">
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
                
                <div class="card-image" style="padding: 15px; display: flex; align-items: center; background: #fafafa;">
                    <img src="{{ asset('storage/' . $despesa->anexo) }}" class="materialboxed" 
                         style="width: 140px; height: 140px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                </div>
                
                <div class="card-stacked">
                    <div class="card-content">
                        <div class="row" style="margin-bottom: 0;">
                            <div class="col s8">
                                <span class="card-title"><b>{{ $despesa->descricao_despesa }}</b></span>
                                <p><b>Fornecedor:</b> {{ $despesa->descricao_fornecedor }}</p>
                                <p><b>Data:</b> {{ date('d/m/Y', strtotime($despesa->data_despesa ?? now())) }}</p>
                            </div>
                            <div class="col s4 right-align">
                                <h5 class="green-text text-darken-2" style="margin: 0;">R$ {{ number_format($despesa->valor, 2, ',', '.') }}</h5>
                                <span class="badge white-text {{ $despesa->status == 'aprovado' ? 'green' : ($despesa->status == 'reprovado' ? 'red' : 'orange') }}" style="border-radius: 4px; float: right; position: relative;">
                                    {{ strtoupper($despesa->status) }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('despesa.status', $despesa->id) }}" method="POST">
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
            @if(!$existePendente) 
                <div class="card-panel green lighten-5" style="border: 2px dashed #2e7d32; border-radius: 12px;">
                    <h5 class="green-text text-darken-3"><b>Análise Concluída!</b></h5>
                    <p>O valor total do reembolso será ajustado para <b>R$ {{ number_format($valorAprovado, 2, ',', '.') }}</b>.</p>
                    @if ($relatorio->status != 'pendente')
                    <button class="btn-large orange darken-2 pulse" style="border-radius: 30px; padding: 0 40px;">
                        <i class="material-icons left">verified_user</i> Relatório já finalizado!
                    </button>
                    @else
                        
                    
                    <form action="{{ route('relatorio.finalizar', $relatorio->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-large green darken-2 pulse" style="border-radius: 30px; padding: 0 40px;">
                            <i class="material-icons left">verified_user</i> FINALIZAR E NOTIFICAR FINANCEIRO
                        </button>
                    </form>

                    @endif
                </div>
            @else
                <div class="card-panel grey lighten-4" style="border-radius: 12px;">
                    <h6 class="orange-text text-darken-2"><i class="material-icons left">info_outline</i> ITENS PENDENTES DE ANÁLISE</h6>
                    <p class="grey-text">Você precisa clicar em "Aprovar" ou "Reprovar" em todos os recibos acima para finalizar este relatório.</p>
                </div>
            @endif
        </div>
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

@endsection
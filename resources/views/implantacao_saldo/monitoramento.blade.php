@extends('layout')
@section('title', 'Monitoramento de Solicitações de Implantação de Saldo')
@section('conteudo')

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

    {{-- for de 1 a 5 --}}
    @for ($i = 1; $i <= 5; $i++)
        @if ($message = Session::get('success'.$i))
            <div class="card green darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Sucesso!</span>
                    <p>Parabéns! A solicitação foi realizada com sucesso!<br>
                    </p>
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
              <p>Corrija os seguintes erros para prosseguir:<br>
                {{$error}}
             </p>
            </div>
          </div>
        @endforeach
      </ul>
  </div>
@endif
</div>

<div class="row">
    <div class="col s12 m8 offset-m2">
        <div class="card">
            <div class="card-content">
                
                    <div class="dashboard-header-zone center-align">
                        <h3 class="brand-title">Monitoramento de <span class="accent-text">Solicitações</span></h3>
                        <p class="brand-tagline">VISUALIZE O STATUS ATUAL DE TODAS AS SOLICITAÇÕES DE IMPLANTAÇÃO DE SALDO</p>
                    </div>
                
                @if(session('success'))
                    <div class="card-panel green white-text">{{ session('success') }}</div>
                @endif

                <table class="highlight centered responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Local</th>
                            <th>Regional</th>
                            <th>Diretor</th>
                            <th>Status Geral</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitacoes as $s)
                        <tr>
                            <td>#{{ $s->id }}</td>
                            <td>{{ $s->produto }}</td>

                            <td>
                                {!! renderIcon($s->aprovacao_filial) !!}
                                <br><small>{{ $s->local->nome ?? 'N/A' }}</small>
                            </td>

                            <td>
                                {!! renderIcon($s->aprovacao_regional) !!}
                                <br><small>{{ $s->regional->nome ?? 'N/A' }}</small>
                            </td>

                            <td>
                                {!! renderIcon($s->aprovacao_diretoria) !!}
                                <br><small>{{ $s->diretores->nome ?? 'N/A' }}</small>
                            </td>

                            <td>
                                @if($s->status == 'reprovado')
                                    <span class="badge red white-text">REPROVADO</span>
                                @elseif($s->status == 'finalizado')
                                    <span class="badge green white-text">FINALIZADO</span>
                                @else
                                    <span class="badge orange white-text">EM ANÁLISE</span>
                                @endif
                            </td>

                            <td>
                                @if($s->status != 'finalizado' && $s->status != 'reprovado')
                                    <a href="{{ route('implantacao_saldo.reenviar_email', $s->id) }}" 
                                    class="btn-floating btn-small waves-effect waves-light blue tooltipped" 
                                    data-position="top" data-tooltip="Reenviar e-mail de cobrança">
                                        <i class="material-icons">email</i>
                                    </a>


                                @elseif($s->status == 'finalizado')
                                    <a href="{{ route('implantacao_saldo.finalizar', $s->id) }}" 
                                    class="btn-floating btn-small waves-effect waves-light green tooltipped" 
                                    data-position="top" data-tooltip="Finalizar Solicitação"
                                    onclick="return confirm('Tem certeza que deseja finalizar esta solicitação?')">
                                        <i class="material-icons">check</i>
                                    </a>
                                @else
                                    <i class="material-icons grey-text">lock</i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
<br><br>

        <div class="card">
            <div class="card-content">
                 <div class="dashboard-header-zone center-align">
                    <h3 class="brand-title">Detalhes da <span class="accent-text">Solicitação</span></h3>
                    <p class="brand-tagline">INFORMAÇÕES COMPLETAS PARA ANÁLISE DETALHADA</p>
                </div> 

                @if(session('success'))
                    <div class="card-panel green white-text">{{ session('success') }}</div>
                @endif

                <table class="highlight centered responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Grupo</th>
                            <th>Subgrupo</th>
                            <th>Quantidade</th>
                            <th>Preço</th>
                            <th>Total</th>
                            <th>Observacao</th>
                            <th>Filial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitacoes as $s)
                        <tr>
                            <td>#{{ $s->id }}</td>
                            <td> <small> {{ $s->descricao_produto }} </small> </td>

                            <td>
                                {{ $s->grupo }}
                            </td>

                            <td>
                                {{ $s->subgrupo }}
                            </td>

                            <td>
                                {{ $s->quantidade }}
                            </td>
                            <td>
                                R$ {{ number_format($s->valor_medio, 2, ',', '.') }}
                            </td>

                            <td>
                                R$ {{ number_format($s->valor_total, 2, ',', '.') }}
                            </td>

                            <td>
                               <small> {{ $s->observacao }} </small>
                            </td>

                            <td>
                               <small> {{ $s->descricao_localizacao }} </small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>

@php
// Helper para renderizar os ícones na própria view
function renderIcon($status) {
    if (is_null($status)) return '<i class="material-icons orange-text" title="Pendente">access_time</i>';
    return $status == 1 
        ? '<i class="material-icons green-text" title="Aprovado">check_circle</i>' 
        : '<i class="material-icons red-text" title="Reprovado">cancel</i>';
}
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.tooltipped');
        M.Tooltip.init(elems);
    });
</script>

@endsection
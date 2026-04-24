<!-- resources/views/minhas-reservas.blade.php -->
@extends('layout')

@section('title', 'Relatório Despesas')
@section('conteudo')

<div class="row">

    @if(auth()->user()->temSetor(['admin', 'suprimentos']))
        <div class="col s12 m6 offset-m3">
    @else
        <div class="col s12 m6 offset-m3">
    @endif
        @if ($message = Session::get('success'))
        <div class="card yellow darken-1">
          <div class="card-content white-text">
            <span class="card-title">Solicitação Cancelada!</span>
            <p>A sua solicitação foi cancelada com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success2'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Solicitação Finalizada!</span>
            <p>A solicitação foi finalizada com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success5'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">E-mail reenviado!</span>
            <p>O e-mail de solicitação fiscal foi reenviado ao <b>{{ Session::get('email_gestor') }}</b> com sucesso!
           </p>
          </div>
        </div>
        @endif






{{-- Relatórios Aprovados --}}
    @if($relatorios->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Relatórios Aprovados</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$relatorios->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    <th class="card-blue">Status</th>
                    <th class="card-blue">Usuário</th>
                    <th class="card-blue">Solicitado</th>
                    <th class="card-blue">Filial</th>
                    <th>Valor</th>

                    <th>Inicio</th>
                    <th>Fim</th>

                    <th>Rodopar</th>
                    <th>Cancelar</th>
                    @if(auth()->user()->temSetor(['admin']))
                        <th class="card-blue">Finalizar</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($relatorios as $r)
                    <tr>
                        <td>{{ $r->status }}</td>
                        <td>{{ $r->user_name}}</td>
                        <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:m:s') }}</td>
                        <td>{{ $r->unidades->unidade_negocio}}</td>
                        <td>R${{ $r->valor }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->ida)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->volta)->format('d/m/Y') }}</td>


                        <td>{{ $r->id_rodopar }}</td>
                       
                        <td>
                            <form action="{{ route('cancelar.relatorio', $r->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">delete</i></button>
                            </form>
                        </td>
                        @if(auth()->user()->temSetor(['admin']))
                        <td>
                            <form action="{{route('finalizar.relatorio', $r->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button>
                            </form>

                        </td>
                        @endif

                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>



@endif



{{-- Despesas pendentes --}}
    @if($despesas->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Despesas Pendentes</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$despesas->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    <th class="card-blue">Status</th>
                    <th class="card-blue">Usuário</th>
                    <th class="card-blue">Solicitado</th>
                    <th class="card-blue">Consumo</th>
                    <th>Fornecedor</th>
                    <th>Despesa</th>
                    <th>Valor</th>
                    <th>Anexo</th>
                    <th class="card-blue">Cancelar</th>

                </tr>
            </thead>
            <tbody>
                @foreach($despesas as $r)
                    @php
                        $extensao = pathinfo($r->anexo, PATHINFO_EXTENSION);
                        $url = asset('storage/' . $r->anexo);
                    @endphp

                    <tr>
                        <td>{{ $r->status }}</td>
                        <td>{{ $r->user_name}}</td>
                        <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:m:s') }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</td>
                        <td>{{ $r->descricao_fornecedor }}</td>
                        <td>{{ $r->descricao_despesa }}</td>
                        <td>R${{ $r->valor }}</td>


                        <td><a href="{{ $url }}" target="_blank">Visualizar</a></td>

                        <td>
                            <form action="{{ route('cancelar.despesa', $r->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">delete</i></button>
                            </form>
                        </td>



                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>



@endif





{{-- Adiantamentos Utilizados  --}}
    @if($adiantamentos->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Adiantamentos Utilizados</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$adiantamentos->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    <th class="card-blue">Status</th>
                    <th class="card-blue">Solicitante</th>
                    <th class="card-blue">Solicitado</th>
                    <th>Fornecedor</th>
                    <th>Valor</th>
                    <th>Utilizado</th>
                    <th>Valor a Receber</th>

                    @if(auth()->user()->temSetor(['admin']))
                        <th class="card-blue">Finalizar</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($adiantamentos as $r)
                    <tr>
                        <td>{{ $r->status }}</td>
                        <td>{{ $r->user_name}}</td>
                        <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:m:s') }}</td>
                        <td>{{ $r->fornecedor }}</td>
                        <td>R${{ $r->valor }}</td>
                        <td>R${{ $r->valor_utilizado }}</td>
                        <td>R${{ $r->liquido }}</td>


                       
                        {{-- <td>
                            <form action="{{ route('finalizar.adiantamento', $r->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button>
                            </form>
                        </td> --}}


                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>



@endif







    </div>
</div>


@endsection

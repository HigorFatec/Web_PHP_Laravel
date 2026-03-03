<!-- resources/views/minhas-reservas.blade.php -->
@extends('layout')

@section('title', 'Minhas Reservas')
@section('conteudo')

<div class="row">

    @if(auth()->user()->admin != 0)
    <div class="col s12 m8 offset-m2">
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






{{-- Reservas de passagens --}}
    @if($relatorios->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Relatórios</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$relatorios->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                    <th class="card-blue">Status</th>
                    <th class="card-blue">Solicitante</th>
                    <th class="card-blue">Solicitado</th>
                    <th class="card-blue">Filial</th>
                    @endif
                    <th>Passagem</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Data de Ida</th>
                    <th>Data de Volta</th>

                    <th>Motivo</th>
                    <th>Viajante</th>
                    <th>Cancelar</th>
                    @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                        <th class="card-blue">Finalizar</th>
                        <th class="card-blue">Filial Viajante</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($relatorios as $relatorio)
                    <tr>
                        @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                        <td>{{ $relatorio->status }}</td>
                        <td>{{ $relatorio->user_name}}</td>
                        <td>{{ \Carbon\Carbon::parse($relatorio->created_at)->format('d/m/Y H:m:s') }}</td>
                        <td>{{ $relatorio->user->filial}}</td>
                        @endif
                        <td>{{ $relatorio->tipo }}</td>
                        <td>{{ $relatorio->origem }}</td>
                        <td>{{ $relatorio->destino }}</td>
                        <td>{{ \Carbon\Carbon::parse($relatorio->ida)->format('d/m/Y') }}</td>
                        <td>    
                        @if($relatorio->volta)
                            {{ \Carbon\Carbon::parse($relatorio->volta)->format('d/m/Y') }}
                        @else

                        @endif
                        </td>


                        <td>{{ $relatorio->motivo }}</td>
                        <td>{{ $relatorio->nome }}</td>
                       
                        <td>
                            <form action="{{ route('cancelar.relatorio', $relatorio->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">delete</i></button>
                            </form>
                        </td>
                        @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                        <td>
                            <form action="{{route('finalizar.relatorio', $relatorio->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button>
                            </form>

                        </td>

                                <td>{{ $relatorio->filial_viajante }}</td>
                        @endif

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

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
            <span class="card-title">Reserva Cancelada!</span>
            <p>A sua reserva foi cancelada com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success2'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Reserva Finalizada!</span>
            <p>A reserva foi finalizada com sucesso!
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

{{-- Reservas Pendentes --}}
    @if($pendentes->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Solicitações de Viagens Pendentes</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$pendentes->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                    <th class="card-blue">Status</th>
                    <th class="card-blue">Solicitante</th>
                    <th class="card-blue">Solicitado</th>
                    <th class="card-blue">Filial</th>
                    @endif
                    <th>Validação</th>
                    <th>Gestor Aprovador</th>
                    <th>Motivo</th>
                    <th>Viajante</th>
                    <th class="card-blue">Cancelar</th>
                    @if(auth()->user()->admin == 1|| auth()->user()->admin == 100)
                        <th class="card-blue">Finalizar</th>
                        <th class="card-blue">Filial Viajante</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($pendentes as $passagem)
                    <tr>
                        @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                        <td>{{ $passagem->status }}</td>
                        <td>{{ $passagem->user_name}}</td>
                        <td>{{ \Carbon\Carbon::parse($passagem->created_at)->format('d/m/Y H:m:s') }}</td>
                        <td>{{ $passagem->user->filial}}</td>
                        @endif
                        <td>{{ $passagem->validacao }}</td>
                        <td>{{ $passagem->email_gestor }}</td>
                        <td>{{ $passagem->motivo }}</td>
                        <td>{{ $passagem->nome }}</td>

                        <td>
                            <form action="{{ route('cancelar.passagem', $passagem->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">delete</i></button>
                            </form>
                        </td>

                        @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)

                        <td>
                            <form action="{{route('finalizar.passagem', $passagem->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button>
                            </form>

                        </td>

                                <td>{{ $passagem->filial_viajante }}</td>
                        @endif

                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>



@endif







{{-- Reservas de hospedagem --}}

@if($pendente_hospedagem->isEmpty())
    <!-- Adicione o conteúdo ou mensagem para quando a lista estiver vazia -->
@else
    <div class="card">
        <div class="card-content">
            <span class="card-title center"><b>Solicitações de Hospedagens Pendentes</b></span>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="row center"> {{$pendente_hospedagem->links('custom.pagination')}} </div>

            <table>
                <thead>
                    <tr>
                        @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                            <th class="card-blue">Status</th>
                            <th class="card-blue">Solicitante</th>
                            <th class="card-blue">Solicitado:</th>
                            <th class="card-blue">Filial</th>
                        @endif
                        <th>Cidade/Hotel</th>
                        <th>Referência</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Motivo</th>
                        <th>Viajante</th>
                        <th class="card-blue">Cancelar</th>
                        @if(auth()->user()->admin == 1|| auth()->user()->admin == 100)
                            <th class="card-blue">Finalizar</th>
                            <th class="card-blue">Filial Viajante</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendente_hospedagem as $ph)
                        <tr>
                            @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                                <td>{{ $ph->status }}</td>
                                <td>{{ $ph->user_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($ph->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $ph->user->filial }}</td>
                            @endif
                            <td>{{ $ph->destino }}</td>
                            <td>{{ $ph->referencia }}</td>
                            <td>{{ \Carbon\Carbon::parse($ph->ida)->format('d/m/Y') }}</td>
                            <td>
                                @if($ph->volta)
                                    {{ \Carbon\Carbon::parse($ph->volta)->format('d/m/Y') }}
                                @endif
                            </td>
                            <td>{{ $ph->motivo }}</td>
                            <td>{{ $ph->nome }}</td>

                            <td>
                                <form action="{{ route('cancelar.hospedagem', $ph->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger red"><i class="material-icons">delete</i></button>
                                </form>
                            </td>

                            @if(auth()->user()->admin == 1 || auth()->user()->admin == 100)
                            <td>
                                <form action="{{route('finalizar.hospedagem', $ph->id)}}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button>
                                </form>

                            </td>

                            <td>{{ $ph->filial_viajante }}</td>
                            @endif
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
@endif

{{-- FIM --}}


    </div>
</div>


@endsection

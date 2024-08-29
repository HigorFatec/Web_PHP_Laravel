<!-- resources/views/minhas-reservas.blade.php -->
@extends('layout')

@section('title', 'Minhas Reservas')
@section('conteudo')

<br>
<div class="row">

    <div class="col s12 m8 offset-m2">

{{-- Reservas de passagens --}}
    @if($passagens->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Minhas Passagens</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$passagens->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 1)
                    <th class="admin">Status</th>
                    <th class="admin">Solicitante</th>
                    <th class="admin">Solicitada: </th>
                    <th class="admin">Filial</th>
                    <th class="admin">Cancelamento</th>
                    @endif
                    <th>Passagem</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Data de Ida</th>
                    <th>Data de Volta</th>
                    <th>Motivo</th>
                    <th>Viajante</th>

                </tr>
            </thead>
            <tbody>
                @foreach($passagens as $passagem)
                    <tr>
                        @if(auth()->user()->admin == 1)
                        <td>{{ $passagem->status }}</td>
                        <td>{{ $passagem->user_name}}</td>
                        <td>{{ \Carbon\Carbon::parse($passagem->created_at)->format('d/m/Y H:m:s') }}</td>
                        <td>{{ $passagem->user->filial}}</td>
                        <td>{{ \Carbon\Carbon::parse($passagem->updated_at)->format('d/m/Y H:m:s') }}</td>
                        @endif
                        <td>{{ $passagem->tipo }}</td>
                        <td>{{ $passagem->origem }}</td>
                        <td>{{ $passagem->destino }}</td>
                        <td>{{ \Carbon\Carbon::parse($passagem->ida)->format('d/m/Y') }}</td>
                        <td>    
                        @if($passagem->volta)
                            {{ \Carbon\Carbon::parse($passagem->volta)->format('d/m/Y') }}
                        @else

                        @endif
                        </td>
                        <td>{{ $passagem->motivo }}</td>
                        <td>{{ $passagem->nome }}</td>
                       

                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>

@endif

{{-- Reservas de veiculos --}}
@if($veiculos->isEmpty())
    @else
        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Minhas Reservas de Veiculo</b></span>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row center"> {{$veiculos->links('custom.pagination')}} </div>

<table>
    <thead>
        <tr>
        <tr>
            @if(auth()->user()->admin == 1)
            <th class="admin">Status</th>
            <th class="admin">Solicitante</th>
            <th class="admin">Solicitada:</th>
            <th class="admin">Filial</th>
            <th class="admin">Cancelamento</th>
            @endif
            <th>Origem</th>
            <th>Destino</th>
            <th>Data de Ida</th>
            <th>Data de Volta</th>
            <th>Motivo</th>
            <th>Viajante</th>

        </tr>
    </thead>
    <tbody>
        @foreach($veiculos as $veiculo)
            <tr>
                @if(auth()->user()->admin == 1)
                <td>{{ $veiculo->status }}</td>
                <td>{{ $veiculo->user_name}}</td>
                <td>{{ \Carbon\Carbon::parse($veiculo->created_at)->format('d/m/Y H:m:s') }}</td>
                <td>{{ $veiculo->user->filial}}</td>
                <td>{{ \Carbon\Carbon::parse($veiculo->updated_at)->format('d/m/Y h:m:s') }}</td>
                @endif
                <td>{{ $veiculo->origem }}</td>
                <td>{{ $veiculo->destino }}</td>
                <td>{{ \Carbon\Carbon::parse($veiculo->ida)->format('d/m/Y') }}</td>
                <td>    
                @if($veiculo->volta)
                    {{ \Carbon\Carbon::parse($veiculo->volta)->format('d/m/Y') }}
                @else
                @endif
                <td>{{ $veiculo->motivo }}</td>
                <td>{{ $veiculo->nome }}</td>
               

            </tr>

        @endforeach
    </tbody>
</table>

</div>
</div>

@endif


{{-- Reservas de hospedagem --}}

@if($hospedagem->isEmpty())
    @else
        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Minhas Hospedagem</b></span>
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row center"> {{$hospedagem->links('custom.pagination')}} </div>

<table>
    <thead>
        <tr>
            @if(auth()->user()->admin == 1)
            <th class="admin">Status</th>
            <th class="admin">Solicitante</th>
            <th class="admin">Solicitada:</th>
            <th class="admin">Filial</th>
            <th class="admin">Cancelamento</th>
            @endif
            <th>Cidade/Hotel</th>
            <th>Referência</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Motivo</th>
            <th>Viajante</th>

        </tr>
    </thead>
    <tbody>
        @foreach($hospedagem as $hospedagem)
            <tr>
                @if(auth()->user()->admin == 1)
                <td>{{ $hospedagem->status }}</td>
                <td>{{ $hospedagem->user_name}}</td>
                <td>{{ \Carbon\Carbon::parse($hospedagem->created_at)->format('d/m/Y H:m:s') }}</td>
                <td>{{ $hospedagem->user->filial}}</td>
                <td>{{ \Carbon\Carbon::parse($hospedagem->updated_at)->format('d/m/Y h:m:s') }}</td>
                @endif
                <td>{{ $hospedagem->destino }}</td>
                <td>{{ $hospedagem->referencia }}</td>
                <td>{{ $hospedagem->ida }}</td>
                <td>{{ $hospedagem->volta }}</td>
                <td>{{ $hospedagem->motivo }}</td>
                <td>{{ $hospedagem->nome }}</td>
               


            </tr>
        @endforeach
    </tbody>
</table>
</div>
</div>

@endif

{{-- Reserva de Adiantamento --}}

@if($adiantamento->isEmpty())
    <!-- Adicione o conteúdo ou mensagem para quando a lista estiver vazia -->
@else

<div class="card">
    <div class="card-content">
        <span class="card-title center"><b>Meus Adiantamentos</b></span>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$adiantamento->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 1)
                        <th class="admin">Status</th>
                        <th class="admin">Solicitante</th>
                        <th class="admin">Solicitada:</th>
                        <th class="admin">Filial</th>
                        <th class="admin">Cancelada</th>
                    @endif
                    <th>Destino</th>
                    <th>Periodo de Inicio</th>
                    <th>Periodo de Fim</th>
                    <th>Motivo</th>
                    <th>Viajante</th>

                </tr>
            </thead>
            <tbody>
                @foreach($adiantamento as $adiant)
                    <tr>
                        @if(auth()->user()->admin == 1)
                            <td>{{ $adiant->status }}</td>
                            <td>{{ $adiant->user_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($adiant->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $adiant->user->filial }}</td>
                            <td>{{ \Carbon\Carbon::parse($adiant->updated_at)->format('d/m/Y H:i:s') }}</td>

                        @endif
                        <td>{{ $adiant->destino }}</td>
                        <td>{{ \Carbon\Carbon::parse($adiant->ida)->format('d/m/Y') }}</td>
                        <td>
                            @if($adiant->volta)
                                {{ \Carbon\Carbon::parse($adiant->volta)->format('d/m/Y') }}
                            @endif
                        </td>
                        <td>{{ $adiant->motivo }}</td>
                        <td>{{ $adiant->nome }}</td>


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

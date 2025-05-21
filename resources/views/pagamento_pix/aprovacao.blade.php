<!-- resources/views/minhas-reservas.blade.php -->
@extends('layout')

@section('title', 'Minhas Reservas')
@section('conteudo')

<div class="row">

    @if(auth()->user()->admin == 2)
    <div class="col s12 m8 offset-m2">
        @else
        <div class="col s12 m6 offset-m3">
            @endif
        @if ($message = Session::get('success'))
        <div class="card yellow darken-1">
          <div class="card-content white-text">
            <span class="card-title">Pagamento Cancelado!</span>
            <p>O pagamento foi cancelado com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success2'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Pagamento Enviado!</span>
            <p>O e-mail de pagamento foi enviado com sucesso!
           </p>
          </div>
        </div>
        @endif

{{-- Reservas de passagens --}}
    @if($aprovar->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Pagamentos Pix</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$aprovar->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 2)
                    <th class="admin">Status</th>
                    <th class="admin">Favorecido</th>
                    <th class="admin">Solicitado</th>
                    <th class="admin">Filial</th>
                    @endif
                    <th>Posto</th>
                    <th>Produto</th>
                    <th>Litragem</th>
                    <th>Valor</th>

                    @if(auth()->user()->admin == 2)
                        <th>Recusar</th>
                        <th class="admin">Aprovar</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($aprovar as $passagem)
                    <tr>
                        @if(auth()->user()->admin == 2)
                        <td>{{ $passagem->status }}</td>
                        <td>{{ $passagem->favorecido}}</td>
                        <td>{{ \Carbon\Carbon::parse($passagem->created_at)->format('d/m/Y H:m:s') }}</td>
                        <td>{{ $passagem->filial}}</td>
                        @endif
                        <td>{{ $passagem->posto }}</td>
                        <td>{{ $passagem->produto }}</td>
                        <td>{{ $passagem->litragem }}</td>
                        <td>{{ $passagem->valor_3 }}</td>


                        </td>
                       
                        <td>
                            <form action="{{ route('cancelar.pagamento', $passagem->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">delete</i></button>
                            </form>
                        </td>
                        @if(auth()->user()->admin == 2)
                        <td>
                            <form action="{{route('finalizar.pagamento', $passagem->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button>
                            </form>

                        </td>

                                <td>{{ $passagem->filial }}</td>
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

<!-- resources/views/minhas-reservas.blade.php -->
@extends('layout')

@section('title', 'Cadastros de Produtos')
@section('conteudo')

<div class="row">

    <div class="col s12 m8 offset-m2">
        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Solicitação Aprovada</span>
                <p>A solicitação foi <b>aprovada</b> com sucesso!
           </p>
          </div>
        </div>
        @endif


        @if ($message = Session::get('success2'))
        <div class="card red darken-1">
          <div class="card-content white-text">
            <span class="card-title">Solicitação Cancelada</span>
            <p>A solicitação foi <b>cancelada</b> com sucesso!
           </p>
          </div>
        </div>
        @endif

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


{{-- Devoluções --}}
    @if($pendente->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Solicitações Cadastro de Produtos</b></span>
                            <div class="row center">{{ $pendente->links('custom.pagination') }}</div>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif


        <table>
            <thead>
                <tr>
                    <th class="card-login">Status</th>
                    <th>Id</th>
                    <th>Solicitante</th>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th class="center">NCM</th>
                    <th>Data Solicitação</th>
                        <th class="card-login">Cancelar</th>
                        <th class="card-login">Finalizar</th>

                </tr>
            </thead>
            <tbody>
                @foreach($pendente as $aprovado)
                    <tr>
                        <td>{{ $aprovado->status }}</td>
                        <td>{{ $aprovado->id }} </td>
                        <td>{{ substr($aprovado->nome_remetente, 0, 15) }}</td>
                        <td>{{ substr($aprovado->nome, 0, 20) }}</td>
                        <td>{{ $aprovado->tipo }}</td>
                        <td class="center"> {{$aprovado->ncm}} </td>


                        
                        <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:m:s') }}</td>


                        
                            <td>
                                <form action="{{ route('produtos.cancelar.form', ['token' => $aprovado->approval_token]) }}" method="GET" style="display:inline;">
                                    <center>
                                        <button type="submit" class="btn btn-danger red darken-1">
                                            <i class="material-icons">close</i>
                                        </button>
                                    </center>
                                </form>
                            </td>

                            <td>
                                <form action="{{ route('produtos.finalizar', ['token' => $aprovado->approval_token]) }}" method="GET" style="display:inline;">
                                    <center>
                                        <button type="submit" class="btn btn-success green">
                                            <i class="material-icons">done</i>
                                        </button>
                                    </center>
                                </form>
                            </td>


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

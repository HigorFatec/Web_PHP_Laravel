<!-- resources/views/minhas-reservas.blade.php -->
@extends('layout')

@section('title', 'Cadastros de Fornecedores')
@section('conteudo')

<div class="row">

    @if(auth()->user()->admin >= 3)
    <div class="col s12 m8 offset-m2">
        @else
        <div class="col s12 m6 offset-m3">
            @endif
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
            <span class="card-title">Solicitação Reprovada</span>
            <p>A solicitação foi <b>reprovada</b> com sucesso!
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
                    <span class="card-title center"><b>Solicitações Pendentes de Aprovação</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif


        <table>
            <thead>
                <tr>
                    <th class="admin">Status</th>
                    <th>Id</th>
                    <th>Solicitante</th>
                    <th>Fornecedor</th>
                    <th>CPF/CNPJ</th>
                    <th>Data Solicitação</th>
                    @if(auth()->user()->admin == 3 || auth()->user()->admin == 100)
                        <th class="admin">Reprovar</th>
                        <th class="admin">Reenviar E-mail</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($pendente as $aprovado)
                    <tr>
                        <td>{{ $aprovado->status }}</td>
                        <td>{{ $aprovado->id }} </td>
                        <td>{{ substr($aprovado->nome_remetente, 0, 15) }}</td>
                        <td>{{ substr($aprovado->razao_social, 0, 20) }}</td>
                        
                        @if(!empty($aprovado->cnpj))
                            <td> {{$aprovado->cnpj}} </td>
                        @else
                            <td> {{$aprovado->cpf}} </td>
                        @endif

                        
                        <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:m:s') }}</td>


                        
                        @if(auth()->user()->admin == 4 || auth()->user()->admin == 100)
                            <td>
                                <form action="{{ route('empresa.reprovar.form', ['token' => $aprovado->approval_token]) }}" method="GET" style="display:inline;">
                                    <center>
                                        <button type="submit" class="btn btn-danger red darken-1">
                                            <i class="material-icons">close</i>
                                        </button>
                                    </center>
                                </form>
                            </td>

                            <td>
                                <form action="{{ route('empresa.aprovar', ['token' => $aprovado->approval_token]) }}" method="GET" style="display:inline;">
                                    <center>
                                        <button type="submit" class="btn btn-success green">
                                            <i class="material-icons">done</i>
                                        </button>
                                    </center>
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

{{-- FIM --}}





    </div>
</div>


@endsection

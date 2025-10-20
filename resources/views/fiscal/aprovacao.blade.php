<!-- resources/views/minhas-reservas.blade.php -->
@extends('layout')

@section('title', 'Pedidos Fiscais')
@section('conteudo')

<div class="row">

    @if(auth()->user()->admin == 3)
    <div class="col s12 m8 offset-m2">
        @else
        <div class="col s12 m6 offset-m3">
            @endif
        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Nota Fiscal emitida!</span>
            <p>A nota fiscal foi alterada para <b>Emitida</b> com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success2'))
        <div class="card yellow darken-1">
          <div class="card-content white-text">
            <span class="card-title">Nota Fiscal emitida!</span>
            <p>A nota fiscal foi alterada para <b>Entrada de Estoque de Filial Pendente</b> com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success3'))
        <div class="card yellow darken-1">
          <div class="card-content white-text">
            <span class="card-title">Nota Fiscal emitida!</span>
            <p>A nota fiscal foi alterada para <b>Credito Pendente</b> com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success4'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Nota Fiscal emitida!</span>
            <p>A nota fiscal foi alterada para <b>Concluido</b> com sucesso!
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
    @if($aprovado_devolucao->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Devoluções Aprovadas</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$aprovado_devolucao->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 3)
                    <th class="admin">Status</th>
                    <th>Id</th>
                    <th>Finalidade da Compra</th>
                    <th>Mercadoria Devolvida</th>
                    <th>Nota Fiscal</th>
                    <th>Fornecedor</th>
                    <th>Valor NF</th>
                    <th>Data Criação</th>
                    <th>Filial</th>
                    <th>Aprovador</th>
                    @endif

                    @if(auth()->user()->admin == 3)
                        <th class="admin">Emitir NF</th>
                        <th class="admin">Credito Pendente</th>
                        <th class="admin">Concluido</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($aprovado_devolucao as $aprovado)
                    <tr>
                        @if(auth()->user()->admin == 3)
                        <td>{{ $aprovado->status }}</td>
                        <td>{{ $aprovado->id }} </td>
                        <td>{{ $aprovado->finalidade_da_compra }}</td>
                        <td>{{ $aprovado->mercadoria_devolvida }}</td>
                        <td>{{ $aprovado->nota_fiscal }}</td>
                        <td>{{ substr($aprovado->fornecedor, 0, 10) }}</td>
                        <td>{{ $aprovado->valor_nf }}</td>

                        <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:m:s') }}</td>
                        @endif
                        <td>{{ $aprovado->filial }}</td>
                        <td>{{ $aprovado->gestor->nome }}</td>


                        </td>

                        @if(auth()->user()->admin == 3)

                        <td>
                            <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">done</i></button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('credito.pendente', $aprovado->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-success orange"> <i class="material-icons">remove</i></button></center>
                            </form>

                        </td>

                        <td>
                            <form action="{{route('fiscal.concluido', $aprovado->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button></center>
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

{{-- Remessas --}}
    @if($aprovado_remessa->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Remessas Aprovadas</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$aprovado_remessa->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 3)
                    <th class="admin">Status</th>
                    <th>Id</th>
                    <th>Tipo de Remessa</th>
                    <th>Cod.Fornecedor Rodopar</th>
                    <th>Remetente</th>
                    <th>Valor NF</th>
                    <th>Data Criação</th>
                    <th>Destinatário</th>
                    <th>Aprovador</th>
                    @endif

                    @if(auth()->user()->admin == 3)
                        <th class="admin">Emitir NF</th>
                        <th class="admin">Pendente Entrada Estoque Filial</th>
                        <th class="admin">Retorno Pendente</th>
                        <th class="admin">Concluido</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($aprovado_remessa as $aprovado)
                    <tr>
                        @if(auth()->user()->admin == 3)
                        <td>{{ $aprovado->status }}</td>
                        <td>{{ $aprovado->id }} </td>
                        <td>{{ $aprovado->tipo_de_venda }}</td>
                        <td>{{ $aprovado->codigo_fornecedor_rodopar }}</td>
                        <td>{{ substr($aprovado->fornecedor, 0, 10) }}</td>
                        <td>{{ $aprovado->valor_nf }}</td>

                        <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:m:s') }}</td>
                        @endif
                        <td>{{ $aprovado->filial }}</td>
                        <td>{{ $aprovado->gestor->nome }}</td>


                        </td>

                        @if(auth()->user()->admin == 3)

                        <td>
                            <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">done</i></button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('filial.pendente', $aprovado->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-success orange"> <i class="material-icons">remove</i></button></center>
                            </form>

                        </td>

                                                <td>
                            <form action="{{route('filial.retorno', $aprovado->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-success pink"> <i class="material-icons">remove</i></button></center>
                            </form>

                        </td>

                        <td>
                            <form action="{{route('fiscal.concluido', $aprovado->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button></center>
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




{{-- Venda --}}
    @if($aprovado_venda->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Vendas Aprovadas</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$aprovado_venda->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 3)
                    <th class="admin">Status</th>
                    <th>Id</th>
                    <th>Tipo de Venda</th>
                    <th>Cliente</th>
                    <th>Info. Adicionais</th>
                    <th>Data Criação</th>
                    <th>Filial</th>
                    <th>Aprovador</th>
                    @endif

                    @if(auth()->user()->admin == 3)
                        <th class="admin">Emitir NF</th>
                        <th class="admin">Concluido</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($aprovado_venda as $aprovado)
                    <tr>
                        @if(auth()->user()->admin == 3)
                        <td>{{ $aprovado->status }}</td>
                        <td>{{ $aprovado->id }} </td>
                        <td>{{ $aprovado->tipo_de_venda }}</td>
                        <td>{{ substr($aprovado->cliente, 0, 10) }}</td>
                        <td>{{ $aprovado->informacoes_adicionais }}</td>

                        <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:m:s') }}</td>
                        @endif
                        <td>{{ $aprovado->filial }}</td>
                        <td>{{ $aprovado->gestor->nome }}</td>


                        </td>

                        @if(auth()->user()->admin == 3)

                        <td>
                            <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">done</i></button>
                            </form>
                        </td>

                        <td>
                            <form action="{{route('fiscal.concluido', $aprovado->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button></center>
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




{{-- Descarte --}}
    @if($aprovado_descarte->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Descartes Aprovados</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$aprovado_descarte->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 3)
                    <th class="admin">Status</th>
                    <th>Id</th>
                    <th>Remetente</th>
                    <th>Motivo Descarte</th>
                    <th>Data Criação</th>
                    <th>Destinatário</th>
                    <th>Aprovador</th>
                    @endif

                    @if(auth()->user()->admin == 3)
                        <th class="admin">Emitir NF</th>
                        <th class="admin">Concluido</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($aprovado_descarte as $aprovado)
                    <tr>
                        @if(auth()->user()->admin == 3)
                        <td>{{ $aprovado->status }}</td>
                        <td>{{ $aprovado->id }} </td>
                        <td>{{ substr($aprovado->fornecedor, 0, 10) }}</td>
                        <td>{{ $aprovado->motivo_operacao }}</td>

                        <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:m:s') }}</td>
                        @endif
                        <td>{{ $aprovado->filial }}</td>
                        <td>{{ $aprovado->gestor->nome }}</td>


                        </td>

                        @if(auth()->user()->admin == 3)

                        <td>
                            <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger red"> <i class="material-icons">done</i></button>
                            </form>
                        </td>

                        <td>
                            <form action="{{route('fiscal.concluido', $aprovado->id)}}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-success green"> <i class="material-icons">done</i></button></center>
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


{{-- Devoluções --}}
    @if($pendente->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Solicitações Pendentes de Aprovação</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row center"> {{$pendente->links('custom.pagination')}} </div>

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin == 3)
                    <th class="admin">Status</th>
                    <th>Id</th>
                    <th>Tipo de Nota Fiscal</th>
                    <th>Fornecedor</th>
                    <th>Valor NF</th>
                    <th>Data Criação</th>
                    <th>Filial</th>
                    <th>Aprovador</th>
                    <th class="admin">Reenviar E-mail</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($pendente as $aprovado)
                    <tr>
                        @if(auth()->user()->admin == 3)
                        <td>{{ $aprovado->status }}</td>
                        <td>{{ $aprovado->id }} </td>
                        <td>{{ $aprovado->tipo }}</td>
                        <td>{{ substr($aprovado->fornecedor, 0, 10) }}</td>
                        <td>{{ $aprovado->valor_nf }}</td>

                        <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:m:s') }}</td>
                        @endif
                        <td>{{ $aprovado->filial }}</td>
                        <td>{{ substr($aprovado->gestor->nome, 0, 10) }}</td>


                        
                        @if(auth()->user()->admin == 3)
                        <td>
                            <form action="{{ route('fiscal.reenviar', $aprovado->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <center><button type="submit" class="btn btn-danger green"> <i class="material-icons">done</i></button></center>
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

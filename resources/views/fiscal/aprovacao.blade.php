@extends('layout')

@section('title', 'Pedidos Fiscais')
@section('conteudo')

<div class="container" style="width: 98%; margin-top: 20px;">
    <div class="row">
        
        {{-- Mensagens de Feedback --}}
        <div class="col s12">
            @php
                $alerts = [
                    'success'  => ['color' => 'green', 'title' => 'Sucesso', 'msg' => 'Nota Fiscal alterada para <b>Emitida</b>.'],
                    'success2' => ['color' => 'orange darken-2', 'title' => 'Sucesso', 'msg' => 'Alterado para <b>Entrada de Estoque Pendente</b>.'],
                    'success3' => ['color' => 'orange darken-2', 'title' => 'Sucesso', 'msg' => 'Alterado para <b>Crédito Pendente</b>.'],
                    'success4' => ['color' => 'green darken-2', 'title' => 'Concluído', 'msg' => 'A solicitação foi <b>Concluída</b> com sucesso.'],
                    'success5' => ['color' => 'blue', 'title' => 'E-mail enviado', 'msg' => 'Solicitação reenviada para <b>' . Session::get('email_gestor') . '</b>.'],
                    'success6' => ['color' => 'red darken-1', 'title' => 'Reprovado', 'msg' => 'A solicitação foi <b>reprovada</b>.'],
                    'success8' => ['color' => 'green darken-2', 'title' => 'Entrega Confirmada', 'msg' => 'A entrega foi <b>confirmada</b> com sucesso.'],

                    ];
            @endphp

            @foreach($alerts as $key => $data)
                @if(Session::has($key))
                    <div class="card {{ $data['color'] }} darken-1">
                        <div class="card-content white-text" style="padding: 15px;">
                            <span class="card-title" style="font-size: 1.1rem; margin-bottom: 0;"><b>{{ $data['title'] }}!</b> {!! $data['msg'] !!}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>



        {{-- Cabeçalho e Legenda --}}
        <div class="col s12">
            <div class="card-panel white z-depth-1" style="padding: 15px; margin-bottom: 25px;">
                <h5 style="margin-top: 0; color: #1a237e;"><b>Controle de Pedidos Fiscais</b></h5>
                <hr>
                <p style="margin-bottom: 5px; font-weight: bold; color: #666;">Legenda de Ações:</p>
                <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                    <div class="legenda-item"><i class="material-icons red-text text-darken-1">close</i> Reprovar</div>
                    <div class="legenda-item"><i class="material-icons blue-text">done</i> Emitir NF</div>
                    <div class="legenda-item"><i class="material-icons green-text">done_all</i> Concluir Pedido</div>
                    <div class="legenda-item"><i class="material-icons orange-text">remove</i> Estoque/Crédito Pendente</div>
                    <div class="legenda-item"><i class="material-icons pink-text">replay</i> Retorno Pendente</div>
                    <div class="legenda-item"><i class="material-icons green-text text-darken-2">email</i> Reenviar E-mail</div>
                    <div class="legenda-item"><i class="material-icons green-text text-darken-2">check_circle</i> Confirmar Entrega</div>
                </div>
            </div>
        </div>



        <div class="col s12">
            
            {{-- 1. DEVOLUÇÕES --}}
            @if(!$aprovado_devolucao->isEmpty())
            <div class="card z-depth-2">
                <div class="card-content">
                    <span class="card-title center"><b>Devoluções Aprovadas</b></span>
                    <div class="row center">{{ $aprovado_devolucao->links('custom.pagination') }}</div>
                    <table class="highlight centered responsive-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>ID</th>
                                <th>Finalidade</th>
                                <th>Mercadoria</th>
                                <th>NF</th>
                                <th>Fornecedor</th>
                                <th>Valor</th>
                                <th>Criação</th>
                                <th>Filial</th>
                                <th>Aprovador</th>
                                @if(auth()->user()->temSetor(['admin', 'aux_fiscal', 'fiscal'])) <th>Ações</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aprovado_devolucao as $aprovado)
                            <tr>
                                <td><span class="badge blue white-text" style="border-radius:2px">{{ $aprovado->status }}</span></td>
                                <td>{{ $aprovado->id }}</td>
                                <td>{{ $aprovado->finalidade_da_compra }}</td>
                                <td>{{ $aprovado->mercadoria_devolvida }}</td>
                                <td>{{ $aprovado->nota_fiscal }}</td>
                                <td>{{ Str::limit($aprovado->fornecedor, 10) }}</td>
                                <td>R$ {{ $aprovado->valor_nf }}</td>
                                <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $aprovado->filial }}</td>
                                <td>{{ $aprovado->gestor->nome }}</td>
                                @if(auth()->user()->temSetor(['admin', 'fiscal']))
                                <td style="display: flex; gap: 4px; justify-content: center;">
                                    <form action="{{ route('fiscal.reprovado', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small red darken-1" title="Reprovar"><i class="material-icons">close</i></button>
                                    </form>
                                    <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small blue" title="Emitir NF"><i class="material-icons">done</i></button>
                                    </form>
                                    <form action="{{ route('credito.pendente', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small orange" title="Crédito Pendente"><i class="material-icons">remove</i></button>
                                    </form>
                                    <form action="{{ route('fiscal.concluido', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small green" title="Concluir"><i class="material-icons">done_all</i></button>
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

            {{-- 2. REMESSAS --}}
            @if(!$aprovado_remessa->isEmpty())
            <div class="card z-depth-2">
                <div class="card-content">
                    <span class="card-title center"><b>Remessas Aprovadas</b></span>
                    <div class="row center">{{ $aprovado_remessa->links('custom.pagination') }}</div>
                    <table class="highlight centered responsive-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Cód Rodopar</th>
                                <th>Remetente</th>
                                <th>Valor</th>
                                <th>Criação</th>
                                <th>Destinatário</th>
                                <th>Aprovador</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aprovado_remessa as $aprovado)
                            <tr>
                                <td>{{ $aprovado->status }}</td>
                                <td>{{ $aprovado->id }}</td>
                                <td>{{ $aprovado->tipo_de_venda }}</td>
                                <td>{{ $aprovado->codigo_fornecedor_rodopar }}</td>
                                <td>{{ $aprovado->filial }}</td>
                                <td>R$ {{ $aprovado->valor_nf }}</td>
                                <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ Str::limit($aprovado->fornecedor, 10) }}</td>
                                <td>{{ $aprovado->gestor->nome }}</td>
                                <td style="display: flex; gap: 4px; justify-content: center;">
                                    {{-- Ações exclusivas de Admin e Auxiliar Fiscal --}}
                                    @if(auth()->user()->temSetor(['admin', 'fiscal']))
                                        <form action="{{ route('fiscal.reprovado', $aprovado->id) }}" method="POST">@csrf
                                            <button class="btn-floating btn-small red darken-1"><i class="material-icons">close</i></button>
                                        </form>
                                        <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST">@csrf
                                            <button class="btn-floating btn-small blue"><i class="material-icons">done</i></button>
                                        </form>
                                        <form action="{{ route('fiscal.concluido', $aprovado->id) }}" method="POST">@csrf
                                            <button class="btn-floating btn-small green"><i class="material-icons">done_all</i></button>
                                        </form>
                                    @endif
                                    
                                    {{-- Ações disponíveis para todos (Fiscal inclusive) --}}
                                    @if(auth()->user()->temSetor(['admin', 'fiscal', 'aux_fiscal']))
                                        <form action="{{ route('filial.pendente', $aprovado->id) }}" method="POST">@csrf
                                            <button class="btn-floating btn-small orange" title="Estoque Pendente"><i class="material-icons">remove</i></button>
                                        </form>
                                        <form action="{{ route('filial.retorno', $aprovado->id) }}" method="POST">@csrf
                                            <button class="btn-floating btn-small pink" title="Retorno Pendente"><i class="material-icons">replay</i></button>
                                        </form>

                                        <form action="{{ route('filial.confirmacao_entrega', $aprovado->id )}}" method="POST">@csrf
                                            <button class="btn-floating btn-small green darken-2" title="Confirmar Entrega"><i class="material-icons">check_circle</i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- 3. VENDAS --}}
            @if(!$aprovado_venda->isEmpty())
            <div class="card z-depth-2">
                <div class="card-content">
                    <span class="card-title center"><b>Vendas Aprovadas</b></span>
                    <div class="row center">{{ $aprovado_venda->links('custom.pagination') }}</div>
                    <table class="highlight centered responsive-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Criação</th>
                                <th>Filial</th>
                                <th>Aprovador</th>
                                @if(auth()->user()->temSetor(['admin', 'aux_fiscal', 'fiscal'])) <th>Ações</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aprovado_venda as $aprovado)
                            <tr>
                                <td>{{ $aprovado->status }}</td>
                                <td>{{ $aprovado->id }}</td>
                                <td>{{ $aprovado->tipo_de_venda }}</td>
                                <td>{{ Str::limit($aprovado->cliente, 10) }}</td>
                                <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $aprovado->filial }}</td>
                                <td>{{ $aprovado->gestor->nome }}</td>
                                @if(auth()->user()->temSetor(['admin', 'fiscal']))
                                <td style="display: flex; gap: 4px; justify-content: center;">
                                    <form action="{{ route('fiscal.reprovado', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small red darken-1"><i class="material-icons">close</i></button>
                                    </form>
                                    <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small blue"><i class="material-icons">done</i></button>
                                    </form>
                                    <form action="{{ route('fiscal.concluido', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small green"><i class="material-icons">done_all</i></button>
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

            {{-- 4. DESCARTES --}}
            @if(!$aprovado_descarte->isEmpty())
            <div class="card z-depth-2">
                <div class="card-content">
                    <span class="card-title center"><b>Descartes Aprovados</b></span>
                    <div class="row center">{{ $aprovado_descarte->links('custom.pagination') }}</div>
                    <table class="highlight centered responsive-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>ID</th>
                                <th>Remetente</th>
                                <th>Criação</th>
                                <th>Filial</th>
                                <th>Aprovador</th>
                                @if(auth()->user()->temSetor(['admin', 'aux_fiscal', 'fiscal'])) <th>Ações</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aprovado_descarte as $aprovado)
                            <tr>
                                <td>{{ $aprovado->status }}</td>
                                <td>{{ $aprovado->id }}</td>
                                <td>{{ Str::limit($aprovado->fornecedor, 10) }}</td>
                                <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $aprovado->filial }}</td>
                                <td>{{ $aprovado->gestor->nome }}</td>
                                @if(auth()->user()->temSetor(['admin',  'fiscal']))
                                <td style="display: flex; gap: 4px; justify-content: center;">
                                    <form action="{{ route('fiscal.reprovado', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small red darken-1"><i class="material-icons">close</i></button>
                                    </form>
                                    <form action="{{ route('emitir.nf', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small blue"><i class="material-icons">done</i></button>
                                    </form>
                                    <form action="{{ route('fiscal.concluido', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small green"><i class="material-icons">done_all</i></button>
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

            {{-- 5. AGUARDANDO GESTOR --}}
            @if(!$pendente->isEmpty())
            <div class="card z-depth-2 grey lighten-4">
                <div class="card-content">
                    <span class="card-title center"><b>Aguardando Aprovação dos Gestores</b></span>
                    <div class="row center">{{ $pendente->links('custom.pagination') }}</div>
                    <table class="highlight centered responsive-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>ID</th>
                                <th>Tipo NF</th>
                                <th>Gestor</th>
                                <th>Fornecedor</th>
                                <th>Valor</th>
                                <th>Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendente as $aprovado)
                            <tr>
                                <td><span class="badge grey darken-1 white-text">{{ $aprovado->status }}</span></td>
                                <td>{{ $aprovado->id }}</td>
                                <td>{{ $aprovado->tipo }}</td>
                                <td>{{ $aprovado->gestor?->nome }}</td>
                                <td>{{ Str::limit($aprovado->fornecedor, 10) }}</td>
                                <td>R$ {{ $aprovado->valor_nf }}</td>
                                <td>{{ \Carbon\Carbon::parse($aprovado->created_at)->format('d/m/Y H:i') }}</td>
                                <td style="display: flex; gap: 4px; justify-content: center;">
                                    @if(auth()->user()->temSetor(['admin', 'aux_fiscal', 'fiscal']))
                                    <form action="{{ route('fiscal.reprovado', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small red darken-1"><i class="material-icons">close</i></button>
                                    </form>
                                    @endif
                                    <form action="{{ route('fiscal.reenviar', $aprovado->id) }}" method="POST">@csrf
                                        <button class="btn-floating btn-small green" title="Reenviar e-mail"><i class="material-icons">email</i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
    .card-title { font-weight: 700; color: #1a237e; margin-bottom: 20px; }
    table thead th { background-color: #f5f5f5; color: #333; font-size: 0.7rem; text-transform: uppercase; }
    .btn-floating:hover { transform: scale(1.15); transition: 0.2s; }
    
    .legenda-item { 
        display: flex; 
        align-items: center; 
        font-size: 0.85rem; 
        color: #444;
        background: #f9f9f9;
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid #e0e0e0;
    }
    .legenda-item i { margin-right: 5px; font-size: 1.2rem; }
</style>

@endsection
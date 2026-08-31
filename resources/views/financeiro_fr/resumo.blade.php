@extends('layout')

@section('title', 'Financeiro - CargoPolo')

@section('conteudo')

@if (@auth()->user()->id != null)

    <div class="login-wrapper">
        <div class="container" style="width: 95%;">
            <div class="row">
                <div class="col s12">
                    
                    {{-- Alertas de Sucesso --}}
                    @if (session('success'))
                        <div class="card green darken-1 shadow-btn" style="border-radius: 8px; margin-bottom: 20px;">
                            <div class="card-content white-text p-1">
                                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="card red darken-1 shadow-btn" style="border-radius: 8px; margin-bottom: 20px;">
                            <div class="card-content white-text p-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
                            </div>
                        </div>
                    @endif


{{-- CARD DE AURA - INTEGRAÇÃO PIX FLOW REAL TIME --}}
<div class="card pix-flow-premium-card shadow-btn" style="border-radius: 15px; margin-bottom: 20px; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); overflow: hidden; position: relative;">
    {{-- Detalhe brilhante no fundo --}}
    <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(99, 102, 241, 0.15); filter: blur(40px); border-radius: 50%; pointer-events: none;"></div>
    
    <div class="card-content" style="padding: 20px 25px;">
        <div class="row" style="margin-bottom: 0; display: flex; align-items: center; flex-wrap: wrap; gap: 15px;">
            
            {{-- Ícone e Título --}}
            <div class="col s12 m7 l8" style="display: flex; align-items: center; gap: 15px;">
                <div class="pix-icon-pulse-wrapper">
                    <i class="fa-brands fa-pix" style="color: #4ade80; font-size: 2rem;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; color: #ffffff; font-weight: 800; font-size: 1.25rem; letter-spacing: 0.5px; display: flex; align-items: center; gap: 10px;">
                        Pix Flow F&R Real Time 
                        <span class="badge-premium-tech">PRO</span>
                    </h5>
                    <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 0.85rem; line-height: 1.4;">
                        Validação automática e conciliação bancária via DICT do Banco Central.
                    </p>
                </div>
            </div>

{{-- O Switch Conectado ao Banco de Dados --}}
<div class="col s12 m5 l4 pix-switch-mobile-align" style="display: flex; justify-content: flex-end; align-items: center;">
    {{-- Passamos o status atual vindo do banco na rota/data-attribute se necessário --}}
    <div class="switch-premium-container tooltipped" 
         id="pix-flow-switch"
         data-status="{{ $status_pix_fr }}"
         data-tooltip="{{ $status_pix_fr == 1 ? 'Módulo ativo em homologação' : 'Módulo desativado' }}" 
         style="cursor: pointer;" 
         onclick="alternarStatusPixFR()">
        
        {{-- Texto OFF fica opaco se estiver ON --}}
        <span class="status-text-pix text-off" style="{{ $status_pix_fr == 1 ? 'color: #475569;' : 'color: #f1f5f9;' }}">OFF</span>
        
        {{-- Se estiver ON (1), mudamos o alinhamento para flex-end e o background para verde --}}
        <div class="custom-premium-switch" style="{{ $status_pix_fr == 1 ? 'justify-content: flex-end; background: #16a34a;' : 'justify-content: flex-start; background: #334155;' }}">
            <div class="custom-switch-handle">
                {{-- Cadeado trancado no OFF, ícone de check ou raio no ON --}}
                <i class="fa-solid {{ $status_pix_fr == 1 ? 'fa-bolt text-green' : 'fa-lock' }}" style="font-size: 10px; color: {{ $status_pix_fr == 1 ? '#16a34a' : '#94a3b8' }};"></i>
            </div>
        </div>
        
        {{-- Texto ON fica brilhante se estiver ON --}}
        <span class="status-text-pix text-on" style="{{ $status_pix_fr == 1 ? 'color: #4ade80; text-shadow: 0 0 10px rgba(74,222,128,0.4);' : 'color: #475569;' }}">ON</span>
    </div>
</div>



        </div>
    </div>
</div>



{{-- BARRA DE BUSCA RÁPIDA CORPORATIVA --}}
<div class="card card-filter-container">
    <div class="card-content" style="padding: 16px 20px;">
        <form id="form-busca-financeiro" method="GET" action="{{ url()->current() }}">
            <div class="row" style="margin-bottom: 0; display: flex; align-items: center; flex-wrap: wrap; gap: 12px 0;">
                
                {{-- Título da Seção --}}
                <div class="col s12 m4">
                    <span class="filter-title">
                        <i class="fa-solid fa-magnifying-glass"></i> Consultar na Base / Filtrar
                    </span>
                </div>

                {{-- Controles de Busca e Ações --}}
                <div class="col s12 m8">
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        
                        {{-- Grouped Select + Input --}}
                        <div class="search-input-group">
                            <select name="tipo" id="tipo-busca" class="browser-default search-select-custom" onchange="filtrarTabelaLocal()">
                                <option value="pedido" {{ request('tipo') == 'pedido' ? 'selected' : '' }}>PEDIDO</option>
                                <option value="id" {{ request('tipo') == 'id' ? 'selected' : '' }}>ID (Sistema)</option>
                            </select>
                            
                            <div class="search-divider"></div>

                            <input type="text" name="busca" id="input-busca-valor" value="{{ request('busca') }}" 
                                oninput="filtrarTabelaLocal()" 
                                placeholder="Digite o número e pressione Enter..." 
                                class="search-input-custom" autocomplete="off">
                        </div>
                        
                        {{-- Botão 1: Filtrar Tabela (GET) --}}
                        <button type="submit" class="btn btn-corp blue darken-4 tooltipped" data-position="top" data-tooltip="Filtrar registros na tabela abaixo">
                            <i class="fa-solid fa-filter"></i> FILTRAR
                        </button>

                        {{-- Botão 2: Resumo Completo via AJAX (Modal) --}}
                        <button type="button" id="btn-detalhes-ajax" onclick="consultarBase()" class="btn btn-corp teal darken-3 tooltipped" data-position="top" data-tooltip="Exibir ficha completa do registro">
                            <i class="fa-solid fa-circle-info"></i> DETALHES
                        </button>

                        {{-- Botão Limpar FiltroAtivo --}}
                        @if(request()->filled('busca'))
                            <a href="{{ url()->current() }}" class="btn-clear-filter tooltipped" data-position="top" data-tooltip="Limpar filtro aplicado">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>






            


                    {{-- TABELA 1: PAGAMENTOS AGUARDANDO --}}
                    <div class="card card-login">
                        <div class="card-content">
                            <div class="center-align mb-2">
                                <h4 class="login-title">PAGAMENTOS APROVADOS PELO GESTOR</h4>
                                <p class="login-subtitle">Anexe os comprovantes para finalizar e disparar os e-mails</p>

                                <div >{{ $financeiro->links('custom.pagination') }}</div>
                                
                            </div>

                            {{-- Navegação Responsiva --}}
                            <div class="center-align">
                                <div class="nav-segment-container">
                                    <a href="{{ route('financeiro.resumo') }}" class="nav-segment-btn {{ Request::routeIs('financeiro.resumo') ? 'active' : '' }}">
                                        <i class="fa-solid fa-clock-check"></i> <span>Aprovados</span>
                                    </a>
                                    <a href="{{ route('financeiro.finalizados') }}" class="nav-segment-btn {{ Request::routeIs('financeiro.finalizados') ? 'active' : '' }}">
                                        <i class="fa-solid fa-circle-check"></i> <span>Finalizados</span>
                                    </a>
                                    <a href="{{ route('financeiro.bi') }}" class="nav-segment-btn {{ Request::routeIs('financeiro.bi') ? 'active' : '' }}">
                                        <i class="fa-solid fa-chart-line"></i> <span>BI</span>
                                    </a>
                                </div>
                            </div>


                            @if($financeiro->isEmpty())
                                <div class="center-align">
                                    <p class="grey-text">Nenhuma solicitação aguardando pagamento.</p>
                                </div>
                            @else
                                <div style="overflow-x: auto;">
                                    <table class="custom-finance-table">
                                        <thead>
                                            <tr style="background-color: #f8f9fa;">
                                                <th class="center">Status</th>
                                                <th style="text-align: left;">Favorecido / Solicitante</th>
                                                <th>Valor / Pedido</th>
                                                <th>Data / Filial</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($financeiro as $r)
                                                {{-- <tr> --}}
                                                @php
                                                    $classePrioridade = '';
                                                    
                                                    if ($r->socorro_em_rota == 'sim' && $r->frota_bloqueada == 'sim') {
                                                        $classePrioridade = 'row-critica'; // Vermelho forte (Urgência Máxima)
                                                    } elseif ($r->socorro_em_rota == 'sim') {
                                                        $classePrioridade = 'row-urgente'; // Vermelho claro (Urgência Padrão)
                                                    }
                                                @endphp

                                                <tr class="{{ $classePrioridade }}">
                                                    
                                                    <td class="center">
                                                        <span class="badge-status">{{ $r->status }}</span>

                                                        {{-- AQUI A MÁGICA ACONTECE REEMBOLSOS --}}
                                                        @if(strtolower($r->tipo) == 'reembolso')
                                                            <div class="badge-reembolso">
                                                                <i class="fa-solid fa-wallet"></i> Reembolso
                                                            </div>
                                                        @endif
                                                        {{-- ADICIONE ESTE BLOCO PARA AJUDA DE CUSTO --}}
                                                        @if(strtolower($r->tipo) == 'ajuda_de_custo')
                                                            <div class="badge-ajuda-custo">
                                                                <i class="fa-solid fa-handshake-angle"></i> Ajuda de Custo
                                                            </div>
                                                        @endif

    
                                                        <div class="sub-info"> #{{ $r->id }}</div>
                                                    </td>
                                                    <td style="text-align: left;">
                                                        <div style="font-weight: bold; color: #1a237e; font-size: 1.1rem;">{{ $r->favorecido }}</div>
                                                        <div class="sub-info">Solicitado por: {{ $r->solicitante }}</div>
                                                    </td>
                                                    <td class="center">
                                                        <div style="font-weight: bold; color: #2e7d32; font-size: 1.1rem;">
                                                            R$ {{ number_format((float)$r->valor, 2, ',', '.') }}
                                                        </div>                                                        
                                                        <div class="sub-info">Ped: {{ $r->pedido }}</div>
                                                    </td>
                                                    <td class="center">
                                                        <div style="font-size: 1rem;">{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y') }}</div>
                                                        <div class="sub-info">{{ $r->unidades->unidade_negocio ?? 'N/I' }}</div>
                                                    </td>

                                                    @if(strtolower($r->tipo) != 'reembolso' && strtolower($r->tipo) != 'ajuda_de_custo')

                                                    <td class="flex-actions">
                                                        <button class="btn-action-detail modal-trigger tooltipped" data-target="modal-detalhes-{{ $r->id }}" data-tooltip="Ver Dados Bancários" onclick="buscarStatusPedido('{{ $r->pedido }}', '{{ $r->id }}')">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </button>

                                                        @if(auth()->user()->temSetor(['admin','financeiro']))

                                                            <a href="{{ route('financeiro.reprovar.form', $r->id) }}" 
                                                                class="btn-action-red tooltipped" 
                                                                style="display: inline-flex; align-items: center; justify-content: center;" 
                                                                data-tooltip="Reprovar"
                                                                onclick="return confirm('Deseja realmente reprovar?')">
                                                                    <i class="fa-solid fa-xmark"></i>
                                                                </a>

                                                            <button class="btn-action-blue modal-trigger tooltipped" data-target="modal-pay-{{ $r->id }}" data-tooltip="Finalizar e Anexar Comprovantes">
                                                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                    @else
                                                        <td class="flex-actions"> {{-- Adicionada a TD para manter o layout --}}
                                                            <form action="{{ route('financeiro.finalizar_reembolso', $r->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn-action-blue tooltipped" data-tooltip="Finalizar" onclick="return confirm('Confirmar finalização?')">
                                                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                                                </button> {{-- Tag de abertura do botão adicionada aqui --}}
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>

                                                {{-- MODAL DE PAGAMENTO (AJUSTADO PARA MÚLTIPLOS ARQUIVOS) --}}
                                                <div id="modal-pay-{{ $r->id }}" class="modal" style="max-width: 500px; border-radius: 15px;">
                                                    <form id="form-pay-{{ $r->id }}" action="{{ route('financeiro.aprovar_financeiro', $r->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf

                                                        <input type="hidden" name="conta_origem" id="hidden-conta-origem-{{ $r->id }}" value="{{ $r->conta_origem ?? '' }}">


                                                        <div class="modal-content">
                                                            <h5 class="login-title center">Finalizar Pagamento</h5>
                                                            <p class="center login-subtitle">ID Solicitação: <strong>#{{ $r->id }}</strong></p>
                                                            <hr style="opacity: 0.1">
                                                            
                                                            <div class="row">
                                                                <div class="input-field col s12">
                                                                    <p class="grey-text" style="font-size: 0.9rem; margin-bottom: 10px;">
                                                                        <i class="fa-solid fa-paperclip icon-blue"></i> Selecione o comprovante (PDF/JPG)
                                                                    </p>
                                                                    <div class="file-field input-field">
                                                                        <div class="btn blue darken-4 btn-small shadow-btn">
                                                                            <span>ARQUIVO</span>
                                                                            <input type="file" name="comprovante" required>
                                                                        </div>
                                                                        <div class="file-path-wrapper">
                                                                            <input class="file-path validate" type="text" placeholder="Selecione um arquivo">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer" style="padding-bottom: 20px; text-align: center; float: none;">
                                                            <a href="#!" class="modal-close btn-flat">Voltar</a>
                                                            <button type="submit" class="btn green darken-3 shadow-btn">CONFIRMAR E ENVIAR <i class="fa-solid fa-check ml-1"></i></button>
                                                        </div>
                                                    </form>
                                                </div>

                                                {{-- MODAL DETALHES BANCÁRIOS --}}
{{-- MODAL DETALHES BANCÁRIOS --}}
<div id="modal-detalhes-{{ $r->id }}" class="modal" style="border-radius: 15px; max-width: 600px;">
    <div class="modal-content">
        <h4 style="color: #1a237e; font-size: 1.4rem; display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <i class="fa-solid fa-circle-info blue-text"></i> Detalhes da Solicitação #{{ $r->id }}
        </h4>
        <hr style="opacity: 0.1; margin-bottom: 20px;">

        <div class="row">
            {{-- Favorecido (Destaque total) --}}
            <div class="col s12" style="margin-bottom: 25px;">
                <h6 class="grey-text uppercase" style="font-size: 0.75rem; font-weight: bold; letter-spacing: 1px;">Favorecido / Beneficiário</h6>
                <p style="font-size: 1.3rem; font-weight: bold; color: #1a237e; margin: 0;">{{ $r->favorecido }}</p>
            </div>

    {{-- Coluna 1: Identificação --}}
    <div class="col s12 m6" style="margin-bottom: 20px;">
        <h6 class="grey-text uppercase" style="font-size: 0.75rem; font-weight: bold; letter-spacing: 1px;">Identificação</h6>
        <div style="background: #fdfdfd; padding: 15px; border-radius: 10px; border: 1px solid #eceff1;">
            <p style="margin: 0; color: #555;"><b>CNPJ/CPF:</b> <br> {{ $r->cnpj ?: 'N/I' }}</p>
            <p style="margin: 10px 0 0 0; color: #555;"><b>Nº Pedido:</b> <br> <span class="blue-text" style="font-weight: bold;">{{ $r->pedido ?: 'N/I' }}</span></p>
            
            {{-- CAMPO DA FILIAL ADICIONADO AQUI --}}
            <p style="margin: 10px 0 0 0; color: #555;">
                <b>Filial:</b> <br> 
                <span id="filial-pedido-{{ $r->id }}" class="grey-text text-darken-2" style="font-weight: bold;">
                    Buscando...
                </span>
            </p>

            {{-- Status do Pedido --}}
            <p style="margin: 10px 0 0 0; color: #555;">
                <b>Status do Pedido:</b> <br> 
                <span id="status-pedido-{{ $r->id }}" class="blue-text" style="font-weight: bold;">
                    Buscando...
                </span>
            </p>
            
            <p style="margin: 10px 0 0 0; color: #555;"><b>Valor:</b> <br> <span class="green-text" style="font-weight: bold;"> 
                @if ($r->status === 'aprovado_gestor')
                    R$ {{ number_format($r->valor, 2, ',', '.') }}
                @else
                    R$ {{$r->valor}}
                @endif
            </span></p>
        </div>
    </div>


{{-- Coluna 2: Dados Bancários --}}
<div class="col s12 m6" style="margin-bottom: 20px;">
    <h6 class="grey-text uppercase" style="font-size: 0.75rem; font-weight: bold; letter-spacing: 1px;">
        <i class="fa-solid fa-building-columns blue-text mr-1"></i> Transferência & Conta Pagadora
    </h6>
    <div style="background: #fdfdfd; padding: 15px; border-radius: 10px; border: 1px solid #eceff1;">
        
        {{-- Dados do Favorecido (Destino) --}}
        <div style="margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px dashed #cfd8dc;">
            <span style="font-size: 0.7rem; font-weight: bold; color: #90a4ae; text-transform: uppercase; letter-spacing: 0.5px;">Dados do Favorecido</span>
            <p style="margin: 4px 0 0 0; color: #555; font-size: 0.85rem;"><b>Banco:</b> {{ $r->banco ?: 'N/I' }}</p>
            <p style="margin: 2px 0 0 0; color: #555; font-size: 0.85rem;"><b>Agência:</b> {{ $r->agencia ?: 'N/I' }}</p>
            <p style="margin: 2px 0 0 0; color: #555; font-size: 0.85rem;"><b>Conta:</b> {{ $r->conta ?: 'N/I' }}</p>
        </div>

        {{-- Seleção da Conta de Origem/Débito --}}
        <div>
            <label style="font-weight: 700; color: #1a237e; font-size: 0.8rem; display: block; margin-bottom: 4px;">
                Conta de Origem para Débito:
            </label>

            @if(auth()->user()->temSetor(['admin','financeiro']))
            
            <select name="conta_origem" form="form-pay-{{ $r->id }}" class="browser-default" 
                    id="select-conta-origem-{{ $r->id }}"
                    style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cfd8dc; background: #ffffff; padding: 0 10px; font-size: 0.85rem; color: #374151; font-weight: 500; cursor: pointer;"
                    onchange="document.getElementById('hidden-conta-origem-{{ $r->id }}').value = this.value;">

                <option value="" disabled selected>Selecione a conta bancária...</option>

                @foreach($contasBancarias as $cb)
                    {{-- 
                        Regra de Exibição:
                        1. Se o pedido não tem filial ($r->cod_filial é NULL), exibe todas as contas.
                        2. Se a conta for global ($cb->cod_filial é NULL), exibe para qualquer pedido.
                        3. Se as filiais forem iguais ($cb->cod_filial == $r->cod_filial), exibe a conta.
                    --}}
                    @if(is_null($r->cod_filial) || is_null($cb->cod_filial) || $cb->cod_filial == $r->cod_filial)
                        <option value="{{ $cb->numero_conta }}" {{ (isset($r->conta_origem) && $r->conta_origem == $cb->numero_conta) ? 'selected' : '' }}>
                            {{ $cb->banco }} - <b>{{ $cb->numero_conta }}</b> {{ $cb->cod_filial ? "(Filial {$cb->cod_filial})" : '(Matriz / Todas)' }}
                        </option>
                    @endif
                @endforeach
            </select>

            @endif
            
        </div>

    </div>
</div>



            {{-- Linha PIX (Destaque para cópia) --}}
            <div class="col s12" style="margin-bottom: 25px;">
                <h6 class="grey-text uppercase" style="font-size: 0.75rem; font-weight: bold; letter-spacing: 1px;">Chave PIX (Clique para copiar)</h6>
                <div class="pix-box tooltipped" 
                     onclick="copyToClipboard('{{ trim($r->pix) }}', 'PIX')" 
                     data-tooltip="Clique para copiar a chave"
                     style="background: #e0f2f1; border: 2px dashed #26a69a; padding: 15px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 15px;">
                    <i class="fa-brands fa-pix" style="color: #26a69a; font-size: 1.5rem;"></i>
                    <span style="font-weight: bold; font-size: 1.1rem; color: #00695c; word-break: break-all;">{{ $r->pix ?: 'Não informado' }}</span>
                </div>
            </div>
        </div>

        <div class="row" style="background: #f9f9f9; padding: 0px; border-radius: 10px; margin: 0;">
            {{-- Configuração Fiscal --}}

            @if(auth()->user()->temSetor(['admin','financeiro']))

            <div class="input-field col s12 m4 offset-m4">
                <h6 class="grey-text uppercase center-align" style="font-size: 0.75rem; font-weight: bold; margin-bottom: 10px;">Configuração Fiscal</h6>
                <select class="browser-default custom-select-nf" onchange="updateFiscalStatus('{{ $r->id }}', this.value)" 
                        style="width: 100%; height: 45px; border-radius: 8px; border: 1px solid #cfd8dc; background: white; cursor: pointer; font-weight: 500;">
                    <option value="sim" {{ $r->tem_nota_fiscal == 'sim' ? 'selected' : '' }}>✅ COM NOTA FISCAL (Lançamento Bancário)</option>
                    <option value="nao" {{ $r->tem_nota_fiscal == 'nao' ? 'selected' : '' }}>❌ SEM NOTA (Contas a Pagar)</option>
                </select>
                <p class="center-align" style="margin-top: 8px; font-size: 0.8rem; color: #78909c;">
                    <i class="fa-solid fa-sync-alt"></i> Atualização automática via sistema
                </p>
            </div>

            @else
                <div class="col s12 center-align" style="margin-top: 20px;">
                    <span class="grey-text" style="font-size: 0.9rem;">Configuração fiscal disponível apenas para administradores.</span>
                </div>
            @endif
            

        </div>
    </div>
    
    
<div class="modal-footer" style="background: #f5f5f5; border-top: 1px solid #e0e0e0; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; height: 70px;">
    
    {{-- Lado Esquerdo: Botão Bonito e Azul --}}
    <div>
        @if(auth()->user()->temSetor(['admin','financeiro']))
            <button class="btn blue darken-4 modal-trigger modal-close shadow-btn" 
                    data-target="modal-pay-{{ $r->id }}" 
                    style="border-radius: 8px; font-weight: bold; text-transform: uppercase; height: 40px;">
                <i class="fa-solid fa-hand-holding-dollar left"></i> Finalizar Pagamento
            </button>
        @endif
    </div>

    {{-- Lado Direito: Botão Fechar --}}
    <div>
        <a href="#!" class="modal-close btn-flat grey-text" style="font-weight: bold;">Fechar</a>
    </div>
</div>

</div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <style>

        .shadow-btn {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.2s ease;
}

.shadow-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 7px 14px rgba(0, 0, 0, 0.12), 0 3px 6px rgba(0, 0, 0, 0.1) !important;
}


        .container { width: 98% !important; }
        .custom-finance-table { font-size: 0.95rem; width: 100%; }
        .custom-finance-table tbody tr { border-bottom: 1px solid #eee; }
        .flex-actions { display: flex; justify-content: center; gap: 5px; }
        .pix-box { background: #f5f5f5; padding: 8px; border-radius: 5px; cursor: pointer; border: 1px dashed #ccc; }
        .btn-action-detail, .btn-action-blue, .btn-action-red {
            border: none; width: 32px; height: 32px; border-radius: 4px; cursor: pointer; color: white;
        }
        .btn-action-detail { background-color: #607d8b; }
        .btn-action-blue { background-color: #1565c0; }
        .btn-action-red { background-color: #d32f2f; }
        .badge-status { background: #e3f2fd; color: #1565c0; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
    
    .pix-box:hover {
    background-color: #b2dfdb !important;
    transform: scale(1.02);
    }
    .custom-select-nf:focus {
        border: 2px solid #1a237e !important;
        outline: none;
    }
    </style>

    <style>
/* Menu de Segmentos Adaptável */
.nav-segment-container {
    display: inline-flex;
    background: #f1f3f4;
    padding: 4px;
    border-radius: 50px;
    border: 1px solid #dee2e6;
    margin-bottom: 15px;
    width: auto;
    max-width: 100%;
}


.nav-segment-btn {
    padding: 8px 18px;
    border-radius: 50px;
    text-decoration: none;
    color: #5f6368;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    gap: 5px;
}

.nav-segment-btn:hover {
    color: #1a73e8;
}

/* Estilo para o botão da página atual */
.nav-segment-btn.active {
    background: #ffffff;
    color: #1a73e8;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.mr-1 { margin-right: 8px; }

.mr-1 { margin-right: 8px; }

/* Estilos específicos para Mobile */
@media (max-width: 600px) {
    .login-title { font-size: 1.2rem; }
    .nav-segment-container { border-radius: 10px; width: 100%; }
    .nav-segment-btn { flex: 1; padding: 10px 5px; font-size: 11px; flex-direction: column; border-radius: 8px; }
    .nav-segment-btn i { font-size: 16px; margin: 0; }
    #embedContainer { height: 60vh; } /* Diminui um pouco a altura no mobile */
}

</style>









<style>
    /* NÍVEL 1: SOCORRO EM ROTA (Atenção) */
    .row-urgente {
        background-color: #fff5f5 !important;
        border-left: 6px solid #ef5350 !important;
    }

    /* NÍVEL 2: SOCORRO + FROTA BLOQUEADA (Crítico/Parada) */
    .row-critica {
        background-color: #ffebee !important; /* Fundo levemente mais rosado */
        border-left: 8px solid #b71c1c !important; /* Borda bem escura e grossa */
    }

    /* Destaque no texto para a linha crítica */
    .row-critica td {
        font-weight: 500; /* Deixa os dados levemente mais visíveis */
    }

    /* Badge de status para o nível crítico */
    .row-critica .badge-status {
        background: #b71c1c !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Animação opcional: Um leve brilho na borda para itens críticos */
    .row-critica td:first-child {
        animation: border-pulse 2s infinite;
    }

    @keyframes border-pulse {
        0% { border-left-color: #b71c1c; }
        50% { border-left-color: #ff5252; }
        100% { border-left-color: #b71c1c; }
    }
</style>

<style>
.badge-reembolso {
    background: #fff3e0; /* Fundo alaranjado claro */
    color: #e65100;     /* Texto laranja escuro */
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: bold;
    display: inline-block;
    margin-top: 5px;
    border: 1px solid #ffe0b2;
    text-transform: uppercase;
}
.badge-ajuda-custo {
    background: #e8eaf6; /* Azul bem clarinho */
    color: #1a237e;     /* Azul escuro */
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: bold;
    display: inline-block;
    margin-top: 5px;
    border: 1px solid #c5cae9;
    text-transform: uppercase;
}
</style>


<style>
/* Custom Styles para o Card de Aura Pix Flow */
.pix-flow-premium-card {
    border: 1px solid rgba(255, 255, 255, 0.08);
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease !important;
}

.pix-flow-premium-card:hover {
    transform: translateY(-2px) !important;
    border-color: rgba(99, 102, 241, 0.3);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.3), 0 4px 8px rgba(99, 102, 241, 0.05) !important;
}

/* Badge Neon */
.badge-premium-tech {
    background: rgba(99, 102, 241, 0.2);
    color: #818cf8;
    border: 1px solid rgba(99, 102, 241, 0.4);
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Animação Pulso no Ícone Pix */
.pix-icon-pulse-wrapper {
    background: rgba(74, 222, 128, 0.1);
    border: 1px solid rgba(74, 222, 128, 0.2);
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 15px rgba(74, 222, 128, 0.1);
}

/* Estrutura do Switch customizado Fake */
.switch-premium-container {
    background: #020617;
    border: 1px solid #334155;
    padding: 6px 12px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    user-select: none;
    transition: all 0.2s ease;
}

.switch-premium-container:hover {
    background: #0f172a;
    border-color: #475569;
}

.status-text-pix {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.status-text-pix.text-off {
    color: #f1f5f9; /* Destacado pois está desligado */
}

.status-text-pix.text-on {
    color: #475569; /* Apagado */
}

.custom-premium-switch {
    width: 46px;
    height: 24px;
    background: #334155;
    border-radius: 15px;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Força o botão a ficar na esquerda (Desativado) */
    position: relative;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);
}

.custom-switch-handle {
    width: 20px;
    height: 20px;
    background: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}
</style>
<style>
    /* Estilização Corporativa da Barra de Consulta */
    .card-filter-container {
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }

    .filter-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-title i {
        color: #1e3a8a;
    }

    /* Container Unificado (Input + Select) */
    .search-input-group {
        display: flex;
        align-items: center;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 2px 4px;
        transition: all 0.2s ease-in-out;
        flex-grow: 1;
    }

    .search-input-group:focus-within {
        border-color: #1e3a8a;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
    }

    .search-select-custom {
        border: none !important;
        background: transparent !important;
        font-weight: 600;
        color: #334155;
        font-size: 0.85rem;
        padding: 0 8px !important;
        height: 36px !important;
        cursor: pointer;
        outline: none;
        width: 125px !important;
    }

    .search-divider {
        width: 1px;
        height: 22px;
        background-color: #cbd5e1;
        margin: 0 6px;
    }

    .search-input-custom {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        height: 36px !important;
        margin: 0 !important;
        padding: 0 10px !important;
        color: #0f172a;
        font-size: 0.9rem;
        flex-grow: 1;
    }

    .search-input-custom:focus {
        border: none !important;
        box-shadow: none !important;
    }

    /* Botões Padrão Corporativo */
    .btn-corp {
        border-radius: 8px !important;
        height: 42px !important;
        line-height: 42px !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
        letter-spacing: 0.4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 16px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        transition: all 0.15s ease-in-out !important;
    }

    .btn-corp:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.12) !important;
    }

    .btn-clear-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        width: 42px;
        border-radius: 8px;
        color: #ef4444;
        background: #fef2f2;
        border: 1px solid #fecaca;
        transition: all 0.15s ease;
    }

    .btn-clear-filter:hover {
        background: #fee2e2;
        color: #dc2626;
    }
</style>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.Modal.init(document.querySelectorAll('.modal'));
            M.Tooltip.init(document.querySelectorAll('.tooltipped'));
        });

        function copyToClipboard(text, tipo) {
            navigator.clipboard.writeText(text).then(() => {
                M.toast({ html: tipo + ' copiado!', classes: 'rounded blue' });
            });
        }
    </script>

<script>
function updateFiscalStatus(id, value) {
    // 1. Pega o token CSRF (importante para o Laravel aceitar o POST)
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                  || '{{ csrf_token() }}';

    // 2. Faz a requisição para a rota que você criou
    fetch(`/atualizar-nota-fiscal/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            tem_nota_fiscal: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            M.toast({
                html: `<i class="fa-solid fa-check-double" style="margin-right:10px"></i> ${data.message}`,
                classes: 'rounded green darken-2'
            });
        } else {
            M.toast({
                html: 'Erro ao atualizar status',
                classes: 'rounded red darken-2'
            });
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        M.toast({
            html: 'Falha na comunicação com o servidor',
            classes: 'rounded red darken-4'
        });
    });
}
</script>

<script>
    function buscarStatusPedido(numped, idRegistro) {
    const spanStatus = document.getElementById('status-pedido-' + idRegistro);
    const spanFilial = document.getElementById('filial-pedido-' + idRegistro); // Opcional: elemento para a filial
    
    // Se o pedido for vazio ou 'N/I'
    if (!numped || numped === 'N/I') {
        spanStatus.innerText = '(N/A)';
        if (spanFilial) spanFilial.innerText = '(N/A)'; // Opcional: atualiza a filial também
        return;
    }

    // Faz a requisição para a rota criada
    fetch(`/consultar-status/${numped}`)
        .then(response => response.json())
        .then(data => {

            if(spanFilial) {
                spanFilial.innerText = data.codfil ?? '(N/A)'; // Atualiza a filial se existir
            }



            spanStatus.innerText = data.situacao;
            
            // Dica: mudar a cor dependendo do texto
            if (data.situacao === 'CANCELADO' || data.situacao === 'REPROVADO') {
                spanStatus.className = 'red-text';
            } else if (data.situacao === 'APROVADO' || data.situacao === 'BAIXADO') {
                spanStatus.className = 'green-text';
            } else {
                spanStatus.className = 'orange-text';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            spanStatus.innerText = 'Erro ao buscar';
        });
}
</script>



<script>
function consultarBase() {
    const input = document.getElementById('input-busca-valor');
    const valor = input.value.trim();
    const tipo = document.getElementById('tipo-busca').value;
    const btnDetails = document.getElementById('btn-detalhes-ajax');

    if (!valor) {
        M.toast({html: '<i class="fa-solid fa-triangle-exclamation style="margin-right:8px;"></i> Informe o número para consulta', classes: 'rounded orange darken-3'});
        input.focus();
        return;
    }

    // Feedback visual de carregamento
    const originalHtml = btnDetails.innerHTML;
    btnDetails.disabled = true;
    btnDetails.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> BUSCANDO...`;

    fetch(`/financeiro/consultar-pedido/${valor}?tipo=${tipo}`)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                const isUrgent = data.socorro_em_rota === 'sim' || data.frota_bloqueada === 'sim';
                
                const modalHtml = `
                    <div style="text-align: left; font-size: 0.9rem; color: #334155;">
                        ${isUrgent ? `
                            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 10px 12px; border-radius: 6px; margin-bottom: 14px; color: #991b1b; font-weight: 600; font-size: 0.85rem;">
                                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> Registro com Alerta de Urgência Operacional
                            </div>
                        ` : ''}
                        
                        <table style="width: 100%; border-collapse: collapse; line-height: 1.8;">
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="font-weight: 600; color: #64748b; width: 40%;">ID Registro:</td>
                                <td style="font-weight: 700; color: #0f172a; text-align: right;">#${data.id}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="font-weight: 600; color: #64748b;">Nº Pedido:</td>
                                <td style="font-weight: 700; color: #0f172a; text-align: right;">${data.pedido || 'N/I'}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="font-weight: 600; color: #64748b;">Favorecido:</td>
                                <td style="color: #0f172a; text-align: right;">${data.favorecido || '-'}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="font-weight: 600; color: #64748b;">Valor:</td>
                                <td style="font-weight: 700; color: #15803d; text-align: right;">R$ ${parseFloat(data.valor || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="font-weight: 600; color: #64748b;">Status:</td>
                                <td style="text-align: right;">
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 12px; font-weight: 600; font-size: 0.75rem; text-transform: uppercase;">
                                        ${data.status}
                                    </span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="font-weight: 600; color: #64748b;">Gestor Responsável:</td>
                                <td style="color: #0f172a; text-align: right;">${data.gestor_aprovador || 'Pendente'}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600; color: #64748b;">Data de Criação:</td>
                                <td style="color: #0f172a; text-align: right;">${data.created_at || '-'}</td>
                            </tr>
                        </table>
                    </div>
                `;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: `<span style="color: #1e3a8a; font-size: 1.2rem; font-weight: 700;">Ficha do Registro</span>`,
                        html: modalHtml,
                        confirmButtonText: 'Fechar',
                        confirmButtonColor: '#1e3a8a',
                        width: '450px'
                    });
                } else if (typeof swal !== 'undefined') {
                    swal({
                        title: `Ficha do Registro #${data.id}`,
                        text: `ID: ${data.id}\nFavorecido: ${data.favorecido}\nStatus: ${data.status}\nValor: R$ ${data.valor}\nTipo: ${data.tipo}\nSocorro: ${data.socorro_em_rota}\nFrota Bloqueada: ${data.frota_bloqueada}\nGestor: ${data.gestor_aprovador || 'N/I'}\nData de Criação: ${data.created_at}\nÚltima Atualização: ${data.updated_at}`,
                        icon: isUrgent ? 'warning' : 'info'
                    });
                }
            } else {
                M.toast({html: `<i class="fa-solid fa-circle-xmark" style="margin-right:8px;"></i> Nenhum registro localizado para ${tipo.toUpperCase()}: ${valor}`, classes: 'rounded red darken-2'});
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            M.toast({html: 'Erro interno ao realizar consulta.', classes: 'rounded red darken-2'});
        })
        .finally(() => {
            btnDetails.disabled = false;
            btnDetails.innerHTML = originalHtml;
        });
}
</script>

<script>
function exibirAvisoHype() {
    M.toast({
        html: '<div style="display:flex; align-items:center; gap:10px;"><i class="fa-solid fa-code-branch text-blue"></i> <span>Módulo Pix Flow em fase final de homologação técnica.</span></div>',
        classes: 'rounded blue-grey darken-4 white-text font-weight-bold'
    });
}
</script>


<script>
function alternarStatusPixFR() {
    const switchElement = document.getElementById('pix-flow-switch');
    // Pega o status atual do elemento (0 ou 1)
    const statusAtual = parseInt(switchElement.getAttribute('data-status'));
    // Inverte o status para o novo valor que queremos salvar
    const novoStatus = statusAtual === 1 ? 0 : 1;

    // Avisa o usuário que a alteração está sendo processada
    M.toast({html: 'Atualizando parâmetros do sistema...', classes: 'blue-grey darken-3'});

    fetch('{{ route("financeiro.atualizarStatusPix") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status_pix_fr: novoStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            M.toast({
                html: '🚀 Configuração atualizada com sucesso!', 
                classes: 'green darken-2'
            });
            
            // Recarrega a página rapidamente para atualizar os estados visuais perfeitamente
            setTimeout(() => window.location.reload(), 800);
        } else {
            M.toast({html: '❌ Erro ao atualizar: ' + data.erro, classes: 'red darken-2'});
        }
    })
    .catch(error => {
        M.toast({html: '❌ Falha de comunicação com o servidor.', classes: 'red darken-2'});
    });
}
</script>







<script>
// Função para filtrar as linhas já carregadas na tela em tempo real
function filtrarTabelaLocal() {
    const tipo = document.getElementById('tipo-busca').value;
    const termoBusca = document.getElementById('input-busca-valor').value.trim().toLowerCase();
    
    // Pega todas as linhas da tabela
    const linhas = document.querySelectorAll('.custom-finance-table tbody tr');

    linhas.forEach(linha => {
        // Se apagar tudo ou estiver vazio, mostra todas as linhas
        if (termoBusca === '') {
            linha.style.display = '';
            return;
        }

        let textoAlvo = '';

        if (tipo === 'id') {
            // Busca o texto na tag .sub-info da 1ª coluna (#ID)
            const divId = linha.querySelector('td:first-child .sub-info');
            textoAlvo = divId ? divId.textContent.replace('#', '').trim().toLowerCase() : '';
        } else if (tipo === 'pedido') {
            // Busca o texto na tag .sub-info da 3ª coluna (Ped: XXX)
            const celulaPedido = linha.children[2];
            if (celulaPedido) {
                const subInfoPed = celulaPedido.querySelector('.sub-info');
                textoAlvo = subInfoPed ? subInfoPed.textContent.replace(/ped:/i, '').trim().toLowerCase() : '';
            }
        }

        // Exibe ou oculta dependendo do termo digitado
        if (textoAlvo.includes(termoBusca)) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }
    });
}
</script>



@else
    <script>
    window.location.href = '/login';
    </script>
@endif

@endsection
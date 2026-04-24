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



                    {{-- BARRA DE BUSCA RÁPIDA --}}
                    <div class="card shadow-btn" style="border-radius: 15px; margin-bottom: 20px; background: #f8f9fa;">
                        <div class="card-content" style="padding: 15px 20px;">
                            <div class="row" style="margin-bottom: 0; display: flex; align-items: center; flex-wrap: wrap;">
                                <div class="col s12 m6">
                                    <span style="font-weight: bold; color: #1a237e;">
                                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Consultar Pedido na Base
                                    </span>
                                </div>
                                <div class="col s12 m6">
                                    <div style="display: flex; gap: 10px;">
                                        <input type="number" id="input-busca-pedido" placeholder="Digite o nº do pedido (Ex: 319040)" 
                                            style="background: white; border: 1px solid #ccc; border-radius: 8px; padding: 0 15px; height: 40px; margin: 0;">
                                        <button type="button" onclick="consultarPedidoBase()" class="btn blue darken-4 shadow-btn" style="border-radius: 8px; height: 40px;">
                                            CONSULTAR
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    {{-- TABELA 1: PAGAMENTOS AGUARDANDO --}}
                    <div class="card card-login">
                        <div class="card-content">
                            <div class="center-align mb-2">
                                <h4 class="login-title">PAGAMENTOS APROVADOS PELO GESTOR</h4>
                                <p class="login-subtitle">Anexe os comprovantes para finalizar e disparar os e-mails</p>
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

                                                    @if(strtolower($r->tipo) != 'reembolso')

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
                                                                <button type="submit" class="btn-action-blue tooltipped" data-tooltip="Finalizar Reembolso" onclick="return confirm('Confirmar finalização de reembolso?')">
                                                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                                                </button> {{-- Tag de abertura do botão adicionada aqui --}}
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>

                                                {{-- MODAL DE PAGAMENTO (AJUSTADO PARA MÚLTIPLOS ARQUIVOS) --}}
                                                <div id="modal-pay-{{ $r->id }}" class="modal" style="max-width: 500px; border-radius: 15px;">
                                                    <form action="{{ route('financeiro.aprovar_financeiro', $r->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
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
                    {{-- Dentro da Coluna 1 do Modal --}}
                    <p style="margin: 10px 0 0 0; color: #555;">
                        <b>Status do Pedido:</b> <br> 
                        <span id="status-pedido-{{ $r->id }}" class="blue-text" style="font-weight: bold;">
                            Buscando...
                        </span>
                    </p>
                    <p style="margin: 0; color: #555;"><b>Valor:</b> <br> <span class="green-text" style="font-weight: bold;"> 
                        
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
                <h6 class="grey-text uppercase" style="font-size: 0.75rem; font-weight: bold; letter-spacing: 1px;">Transferência TED/DOC</h6>
                <div style="background: #fdfdfd; padding: 15px; border-radius: 10px; border: 1px solid #eceff1;">
                    <p style="margin: 0; color: #555;"><b>Banco:</b> {{ $r->banco ?: 'N/I' }}</p>
                    <p style="margin: 5px 0 0 0; color: #555;"><b>Agência:</b> {{ $r->agencia ?: 'N/I' }}</p>
                    <p style="margin: 5px 0 0 0; color: #555;"><b>Conta:</b> {{ $r->conta ?: 'N/I' }}</p>
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
    
    // Se o pedido for vazio ou 'N/I'
    if (!numped || numped === 'N/I') {
        spanStatus.innerText = '(N/A)';
        return;
    }

    // Faz a requisição para a rota criada
    fetch(`/consultar-status/${numped}`)
        .then(response => response.json())
        .then(data => {
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
function consultarPedidoBase() {
    const pedido = document.getElementById('input-busca-pedido').value;

    if (!pedido) {
        M.toast({html: 'Digite um número de pedido', classes: 'rounded orange'});
        return;
    }

    M.toast({html: 'Consultando base de dados...', classes: 'rounded blue'});

    fetch(`/financeiro/consultar-pedido/${pedido}`)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                const infoText = `ID: ${data.id}\nFavorecido: ${data.favorecido}\nStatus: ${data.status}\nValor: R$ ${data.valor}\nTipo: ${data.tipo}\nSocorro: ${data.socorro_em_rota}\nFrota Bloqueada: ${data.frota_bloqueada}\nGestor Aprovador: ${data.gestor_aprovador || 'N/I'}`;

                // Verifica se o swal existe antes de chamar para evitar o erro de ReferenceError
                if (typeof swal !== 'undefined') {
                    swal({
                        title: `Pedido #${pedido}`,
                        text: infoText,
                        icon: (data.socorro_em_rota === 'sim' || data.frota_bloqueada === 'sim') ? 'warning' : 'info',
                    });
                } else {
                    // Fallback caso o SweetAlert falhe: usa um alert padrão organizado
                    alert(`--- DADOS DO PEDIDO ${pedido} ---\n${infoText}`);
                }

                if (data.socorro_em_rota === 'sim') {
                    M.toast({html: '⚠️ URGÊNCIA: Socorro em Rota!', classes: 'rounded red'});
                }
            } else {
                M.toast({html: 'Pedido não encontrado', classes: 'rounded red'});
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            M.toast({html: 'Erro na consulta. Verifique o console.', classes: 'rounded red'});
        });
}
</script>



@else
    <script>
    window.location.href = '/login';
    </script>
@endif

@endsection
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


                    {{-- TABELA 2: PAGAMENTOS FINALIZADOS (COM DOWNLOAD DO ZIP) --}}
                    <div class="card card-login" style="margin-top: 30px;">
                        <div class="card-content">
                            <div class="center-align mb-2">
                                <h4 class="login-title">HISTÓRICO DE PAGAMENTOS FINALIZADOS</h4>
                                <div >{{ $finalizados->links('custom.pagination') }}</div>

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

                            


                            @if($finalizados->isEmpty())
                                <div class="center-align">
                                    <p class="grey-text">Nenhum pagamento finalizado recentemente.</p>
                                </div>
                            @else
                                <div style="overflow-x: auto;">
    <table class="highlight centered custom-finance-table">
        <thead>
            <tr>
                <th class="center">Status</th>
                <th>Favorecido</th>
                <th>Valor / Pedido</th>
                <th>Data Pagto</th>
                <th>SLA (Tempo de Atendimento)</th> {{-- Nova Coluna --}}
                <th>Comprovantes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($finalizados as $r)
                @php
                    $criado = \Carbon\Carbon::parse($r->created_at);
                    $pago = \Carbon\Carbon::parse($r->updated_at);
                    
                    // Diferença total em horas para lógica de cores
                    $diffHoras = $criado->diffInHours($pago);
                    
                    // Texto amigável (ex: "2 dias", "5 horas")
                    $tempoSla = $criado->diffForHumans($pago, [
                        'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                        'parts' => 2 // Exibe "1 dia e 2 horas"
                    ]);
                @endphp
                <tr>
                    <td class="center">
                        <span class="badge green white-text" style="border-radius: 4px;">PAGO</span>
                        <div class="sub-info"> #{{ $r->id }}</div>
                    </td>
                    <td>{{ $r->favorecido }}</td>
                    <td class="center">
                        <div style="font-weight: bold; color: #2e7d32; font-size: 1.1rem;">R$ {{ number_format($r->valor, 2, ',', '.') }}</div>
                        <div class="sub-info">Ped: {{ $r->pedido }}</div>
                    </td>                    <td>
                        <div style="font-size: 0.9rem;">{{ $pago->format('d/m/Y H:i') }}</div>
                        <div class="sub-info" style="font-size: 0.75rem;">Solicitado em: {{ $criado->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        {{-- Badge Dinâmico de SLA --}}
                        <span class="tooltipped" data-tooltip="Pedido feito em: {{ $criado->format('d/m/Y H:i') }}"
                              style="font-weight: bold; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; 
                              background-color: {{ $diffHoras <= 24 ? '#e8f5e9' : ($diffHoras <= 48 ? '#fff3e0' : '#ffebee') }};
                              color: {{ $diffHoras <= 24 ? '#2e7d32' : ($diffHoras <= 48 ? '#ef6c00' : '#c62828') }};
                              border: 1px solid {{ $diffHoras <= 24 ? '#c8e6c9' : ($diffHoras <= 48 ? '#ffe0b2' : '#ffcdd2') }};">
                            <i class="fa-regular fa-clock" style="font-size: 0.8rem; margin-right: 4px;"></i>
                            {{ $tempoSla }}
                        </span>
                    </td>
                    <td>
                        @if($r->comprovante_pagamento)
                            @php $isPdf = str_contains($r->comprovante_pagamento, '.pdf'); @endphp
                            <a href="{{ asset('storage/' . $r->comprovante_pagamento) }}" target="_blank" 
                               class="btn-floating btn-small {{ $isPdf ? 'red' : 'blue' }} waves-effect tooltipped" 
                               data-tooltip="Ver Comprovante">
                                <i class="fa-solid {{ $isPdf ? 'fa-file-pdf' : 'fa-image' }}"></i>
                            </a>
                        @else
                            <span class="grey-text">Sem anexo</span>
                        @endif
                    </td>
                </tr>
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

        /* Estilos específicos para Mobile */
        @media (max-width: 600px) {
            .login-title { font-size: 1.2rem; }
            .nav-segment-container { border-radius: 10px; width: 100%; }
            .nav-segment-btn { flex: 1; padding: 10px 5px; font-size: 11px; flex-direction: column; border-radius: 8px; }
            .nav-segment-btn i { font-size: 16px; margin: 0; }
            #embedContainer { height: 60vh; } /* Diminui um pouco a altura no mobile */
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

@else
    <script>    
    window.location.href = '/login';
    </script>
@endif

@endsection
@extends('layout')

@section('title', 'Minhas Reservas')
@section('conteudo')

<div class="row">
    {{-- Ajuste de largura baseado no nível de acesso --}}
    @if(auth()->user()->temSetor(['admin', 'suprimentos']))
        <div class="col s12 m10 offset-m1">
    @else
        <div class="col s12 m8 offset-m2">
    @endif

        {{-- Alertas de Feedback --}}
        @php
            $alerts = [
                'success'  => ['color' => 'yellow darken-1', 'title' => 'Reserva Cancelada!', 'msg' => 'A sua reserva foi cancelada com sucesso!'],
                'success2' => ['color' => 'green darken-1', 'title' => 'Reserva Finalizada!', 'msg' => 'A reserva foi finalizada com sucesso!'],
                'success5' => ['color' => 'green darken-1', 'title' => 'E-mail reenviado!', 'msg' => 'O e-mail foi enviado ao <b>' . Session::get('email_gestor') . '</b> com sucesso!'],
            ];
        @endphp

        @foreach($alerts as $key => $data)
            @if(Session::has($key))
                <div class="card {{ $data['color'] }}">
                    <div class="card-content white-text">
                        <span class="card-title">{{ $data['title'] }}</span>
                        <p>{!! $data['msg'] !!}</p>
                    </div>
                </div>
            @endif
        @endforeach

{{-- Navegação Flutuante Premium --}}
<div class="row" style="margin-top: 20px;">
    <div class="col s12 center-align">
        <div class="floating-nav-wrapper">
            <a href="{{ route('reserva.reservas') }}" class="nav-item-premium {{ Request::routeIs('reserva.reservas') ? 'active' : '' }}">
                <div class="icon-box"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="nav-text">
                    <span class="main-txt">Reservas</span>
                    <span class="sub-txt">Aprovadas</span>
                </div>
            </a>

            <div class="nav-divider"></div>

            <a href="{{ route('reserva.bi') }}" class="nav-item-premium {{ Request::routeIs('reserva.bi') ? 'active' : '' }}">
                <div class="icon-box"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="nav-text">
                    <span class="main-txt">Analytics</span>
                    <span class="sub-txt">Business Intelligence</span>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- Legenda Estilo "Quick Info" --}}
@if(auth()->user()->temSetor(['admin', 'suprimentos']))
<div class="row" style="margin-bottom: 35px;">
    <div class="col s12">
        <div class="quick-action-legend">
            <div class="action-tag">
                <span class="dot red-dot"></span>
                <span class="action-label"><b>Excluir:</b> Cancela a solicitação</span>
            </div>
            <div class="action-tag">
                <span class="dot green-dot"></span>
                <span class="action-label"><b>Check:</b> Finaliza o processo</span>
            </div>
        </div>
    </div>
</div>
@endif

        {{-- 1. PASSAGENS --}}
        @if(!$passagens->isEmpty())
        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Minhas Passagens</b></span>
                <div class="row center">{{ $passagens->links('custom.pagination') }}</div>
                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <th class="card-blue">Status</th>
                                <th class="card-blue">Solicitante</th>
                                <th class="card-blue">Solicitado</th>
                            @endif
                            <th>Tipo</th>
                            <th>Origem/Destino</th>
                            <th>Datas</th>
                            <th>Viajante</th>
                            <th>Ações</th>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <th class="card-blue">Filial Viajante</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($passagens as $passagem)
                        <tr>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <td>{{ $passagem->status }}</td>
                                <td>{{ $passagem->user_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($passagem->created_at)->format('d/m/Y H:i') }}</td>
                            @endif
                            <td>{{ $passagem->tipo }}</td>
                            <td>{{ $passagem->origem }} <i class="material-icons tiny">arrow_forward</i> {{ $passagem->destino }}</td>
                            <td>
                                <b>Ida:</b> {{ \Carbon\Carbon::parse($passagem->ida)->format('d/m/Y') }}<br>
                                @if($passagem->volta) <b>Volta:</b> {{ \Carbon\Carbon::parse($passagem->volta)->format('d/m/Y') }} @endif
                            </td>
                            <td>{{ $passagem->nome }}</td>
                            <td>
                                <form action="{{ route('cancelar.passagem', $passagem->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn-floating btn-small red"><i class="material-icons">delete</i></button>
                                </form>
                                @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <form action="{{ route('finalizar.passagem', $passagem->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn-floating btn-small green"><i class="material-icons">done</i></button>
                                </form>
                                @endif
                            </td>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <td>{{ $passagem->filial_viajante }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- 2. VEÍCULOS --}}
        @if(!$veiculos->isEmpty())
        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Minhas Reservas de Veículo</b></span>
                <div class="row center">{{ $veiculos->links('custom.pagination') }}</div>
                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <th class="card-blue">Status</th>
                                <th class="card-blue">Solicitante</th>
                            @endif
                            <th>Origem/Destino</th>
                            <th>Datas</th>
                            <th>Motivo</th>
                            <th>Viajante</th>
                            <th>Ações</th>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <th class="card-blue">Filial Viajante</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($veiculos as $veiculo)
                        <tr>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <td>{{ $veiculo->status }}</td>
                                <td>{{ $veiculo->user_name }}</td>
                            @endif
                            <td>{{ $veiculo->origem }} <i class="material-icons tiny">arrow_forward</i> {{ $veiculo->destino }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($veiculo->ida)->format('d/m/Y') }} 
                                @if($veiculo->volta) a {{ \Carbon\Carbon::parse($veiculo->volta)->format('d/m/Y') }} @endif
                            </td>
                            <td>{{ Str::limit($veiculo->motivo, 20) }}</td>
                            <td>{{ $veiculo->nome }}</td>
                            <td>
                                <form action="{{ route('cancelar.veiculo', $veiculo->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn-floating btn-small red"><i class="material-icons">delete</i></button>
                                </form>
                                @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <form action="{{ route('finalizar.veiculo', $veiculo->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn-floating btn-small green"><i class="material-icons">done</i></button>
                                </form>
                                @endif
                            </td>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <td>{{ $veiculo->filial_viajante }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- 3. HOSPEDAGEM --}}
        @if(!$hospedagem->isEmpty())
        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Minhas Hospedagens</b></span>
                <div class="row center">{{ $hospedagem->links('custom.pagination') }}</div>
                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <th class="card-blue">Status</th>
                                <th class="card-blue">Solicitante</th>
                            @endif
                            <th>Cidade/Hotel</th>
                            <th>Check-In/Out</th>
                            <th>Viajante</th>
                            <th>Ações</th>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <th class="card-blue">Filial Viajante</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hospedagem as $hosp)
                        <tr>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <td>{{ $hosp->status }}</td>
                                <td>{{ $hosp->user_name }}</td>
                            @endif
                            <td>{{ $hosp->destino }} <br> <small>{{ $hosp->referencia }}</small></td>
                            <td>
                                {{ \Carbon\Carbon::parse($hosp->ida)->format('d/m/Y') }} 
                                @if($hosp->volta) <br> até {{ \Carbon\Carbon::parse($hosp->volta)->format('d/m/Y') }} @endif
                            </td>
                            <td>{{ $hosp->nome }}</td>
                            <td>
                                <form action="{{ route('cancelar.hospedagem', $hosp->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn-floating btn-small red"><i class="material-icons">delete</i></button>
                                </form>
                                @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <form action="{{ route('finalizar.hospedagem', $hosp->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn-floating btn-small green"><i class="material-icons">done</i></button>
                                </form>
                                @endif
                            </td>
                            @if(auth()->user()->temSetor(['admin', 'suprimentos']))
                                <td>{{ $hosp->filial_viajante }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>

<style>
    /* Container da Navegação Flutuante */
    .floating-nav-wrapper {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        padding: 10px 25px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
    }

    .nav-item-premium {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 15px;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .nav-item-premium .icon-box {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f7fa;
        color: #90a4ae;
        border-radius: 10px;
        font-size: 1.2rem;
        transition: 0.3s;
    }

    .nav-item-premium .nav-text {
        display: flex;
        flex-direction: column;
        text-align: left;
    }

    .nav-item-premium .main-txt {
        color: #37474f;
        font-weight: 700;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .nav-item-premium .sub-txt {
        color: #90a4ae;
        font-size: 0.7rem;
        font-weight: 500;
    }

    /* Estado Ativo */
    .nav-item-premium.active .icon-box {
        background: #e3f2fd;
        color: #1e88e5;
    }

    .nav-item-premium.active .main-txt { color: #1e88e5; }

    .nav-item-premium:hover:not(.active) { background: #fcfcfc; }

    .nav-divider {
        width: 1px;
        height: 35px;
        background: #eee;
        margin: 0 20px;
    }

    /* Estilo da Legenda Discreta */
    .quick-action-legend {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 10px;
    }

    .action-tag {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .red-dot { background: #ff5252; box-shadow: 0 0 8px rgba(255,82,82,0.4); }
    .green-dot { background: #4caf50; box-shadow: 0 0 8px rgba(76,175,80,0.4); }

    .action-label {
        font-size: 0.8rem;
        color: #607d8b;
    }

    @media (max-width: 600px) {
        .floating-nav-wrapper { flex-direction: column; width: 100%; padding: 15px; }
        .nav-divider { width: 80%; height: 1px; margin: 15px 0; }
        .nav-item-premium { width: 100%; justify-content: flex-start; }
        .quick-action-legend { flex-direction: column; align-items: center; gap: 10px; }
    }
</style>

@endsection
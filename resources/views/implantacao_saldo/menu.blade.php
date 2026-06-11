@extends('layout')
@section('title', 'Ajuste de Estoque - Menu')
@section('conteudo')

@if (auth()->user()->id != null)

<div class="container" style="padding-top: 50px; width: 95%;">
    
    {{-- BLOCO DE ALERTAS E SUCESSO --}}
    <div class="col s12 m6 offset-m3">
        @if ($message = Session::get('success'))
            <div class="card green darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Sucesso!</span>
                    <p>Parabéns! A solicitação foi realizada com sucesso!</p>
                </div>
            </div>
        @endif

        @for ($i = 1; $i <= 5; $i++)
            @if ($message = Session::get('success'.$i))
                <div class="card green darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Sucesso!</span>
                        <p>Parabéns! A solicitação foi realizada com sucesso!</p>
                    </div>
                </div>
            @endif
        @endfor

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div class="card red darken-1">
                        <div class="card-content white-text">
                            <span class="card-title">Erro</span>
                            <p>Corrija os seguintes erros para prosseguir:<br>{{$error}}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- CABEÇALHO ESTILIZADO --}}
    <div class="dashboard-header-zone center-align">
        <h3 class="brand-title">Estoque <span class="accent-text">Almoxarifado</span></h3>
        <p class="brand-tagline">IMPLANTAÇÃO DE SALDO DE ESTOQUE</p>
    </div>

    {{-- GRID PREMIUM --}}
    <div class="balanced-grid">
        
        {{-- CARD 1: Implantação de Saldo --}}
        <a href="{{ route('implantacao_saldo.monitoramento') }}" class="premium-card master-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="live-indicator">
                    <span class="dot"></span> Monitoramento
                </div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Painel de Controle</h4>
                <p class="item-desc">Monitoramento de implantação de saldo, controle de aprovação e reenvio de solicitações</p>
            </div>
            <div class="card-action">
                <span>Gerenciar Implantação</span>
                <i class="fa-solid fa-arrow-right-long"></i>
            </div>
        </a>

        {{-- CARD 2: Implantação de Estoque --}}
        <a href="{{ route('implantacao_saldo.index') }}" class="premium-card request-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon request-icon">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <div class="status-badge">ACESSO RÁPIDO</div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Nova Solicitação</h4>
                <p class="item-desc">Módulo exclusivo para solicitação de ajuste de estoque</p>
            </div>
            <div class="card-action">
                <span>Abrir Formulário</span>
                <i class="fa-solid fa-plus"></i>
            </div>
        </a>



      

    </div>
</div>



@else
<script>
    window.location.href = '/login';
</script>
@endif

@endsection
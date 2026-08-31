@extends('layout')
@section('title', 'Portal de Combustível')
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
        <h3 class="brand-title">Gestão de <span class="accent-text">Combustível</span></h3>
        <p class="brand-tagline">CONTROLE DE SALDOS E PAGAMENTOS INSTANTÂNEOS</p>
    </div>

    {{-- GRID PREMIUM --}}
    <div class="balanced-grid">

        {{-- CARD NOVO: IMPORTAÇÃO DE ABASTECIMENTOS --}}
        <a href="{{ route('abastecimento.importar.index') }}" class="premium-card visa-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon">
                    <i class="fa-solid fa-file-csv"></i>
                </div>
                <div class="live-indicator">
                    <span class="dot"></span> NOVO
                </div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Importação de Abastecimentos</h4>
                <p class="item-desc">Realize a integração em massa de abastecimentos (arquivos .CSV) de forma automatizada no sistema.</p>
            </div>
            <div class="card-action">
                <span>Acessar Importador</span>
                <i class="fa-solid fa-arrow-right-long"></i>
            </div>
        </a>
        
        {{-- CARD 1: PAGAMENTO PIX --}}
        <a href="{{ route('pagamento_pix.index') }}" class="premium-card request-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon request-icon">
                    <i class="fa-brands fa-pix"></i>
                </div>
                <div class="status-badge">PAGAMENTO</div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Pagamento Pix</h4>
                <p class="item-desc">Solicitação de pagamentos instantâneos para abastecimentos gerais da frota.</p>
            </div>
            <div class="card-action">
                <span>Acessar Módulo</span>
                <i class="fa-solid fa-plus"></i>
            </div>
        </a>

        {{-- CARD 2: FLORESTAL PIX --}}
        <a href="{{ route('florestal_pix.index') }}" class="premium-card request-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon request-icon">
                    <i class="fa-solid fa-tree"></i>
                </div>
                <div class="status-badge">FLORESTAL</div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Florestal Pix</h4>
                <p class="item-desc">Módulo exclusivo para pagamentos via Pix destinados à operação florestal.</p>
            </div>
            <div class="card-action">
                <span>Solicitar Pagamento</span>
                <i class="fa-solid fa-plus"></i>
            </div>
        </a>

        {{-- CARD 3: REDE FROTAS --}}
        <a href="{{ route('saldo.index') }}" class="premium-card master-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon">
                    <i class="fa-solid fa-gas-pump"></i>
                </div>
                <div class="live-indicator">
                    <span class="dot"></span> ONLINE
                </div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Saldo Rede Frotas</h4>
                <p class="item-desc">Consulta de saldos, limites disponíveis e gestão de abastecimentos na Rede Frotas.</p>
            </div>
            <div class="card-action">
                <span>Verificar Saldo</span>
                <i class="fa-solid fa-arrow-right-long"></i>
            </div>
        </a>

        {{-- CARD 4: VALECARD --}}
        <a href="{{ route('saldo.valecard') }}" class="premium-card master-card">
            <div class="shimmer"></div>
            <div class="card-top">
                <div class="main-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="live-indicator">
                    <span class="dot"></span> ONLINE
                </div>
            </div>
            <div class="card-info">
                <h4 class="item-title">Saldo ValeCard</h4>
                <p class="item-desc">Acesso rápido ao saldo e extrato de cartões combustível da bandeira ValeCard.</p>
            </div>
            <div class="card-action">
                <span>Verificar Saldo</span>
                <i class="fa-solid fa-arrow-right-long"></i>
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
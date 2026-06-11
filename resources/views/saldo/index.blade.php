@extends('layout')
@section('title', 'Saldo de Combustível Rede Frota')

@section('conteudo')

@if (auth()->user()->id != null)

<div class="container" style="padding-top: 50px; max-width: 500px;">
    
    {{-- CABEÇALHO --}}
    <div class="dashboard-header-zone center-align">
        <h3 class="brand-title">Rede <span class="accent-text">Frota</span></h3>
        <p class="brand-tagline">CONSULTA DE SALDO ATUALIZADO</p>
    </div>

    {{-- CARD DE SALDO PREMIUM --}}
    <div class="premium-card master-card center-align">
        <div class="shimmer"></div>
        
        <div class="card-top" style="justify-content: center;">
            <div class="main-icon">
                <i class="fa-solid fa-gas-pump"></i>
            </div>
        </div>

        <div class="card-info">
            <h5 style="color: rgba(255,255,255,0.7); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Saldo Disponível</h5>
            <h2 class="item-title" style="font-size: 3rem; margin: 10px 0; color: var(--neon-cyan);">
                {{ $saldo->valor }}
            </h2>
            <p class="item-desc" style="font-size: 0.85rem; opacity: 0.6;">
                <i class="fa-regular fa-clock"></i> 
                Atualizado em: {{ \Carbon\Carbon::parse($saldo->data_insercao)->format('d/m/Y H:i') }}
            </p>
        </div>

        {{-- BOTÃO ATUALIZAR CENTRALIZADO --}}
        <div class="card-action" style="justify-content: center; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 20px; padding-top: 20px;">
            <a href="/saldo" style="color: white; display: flex; align-items: center; gap: 10px; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fa-solid fa-rotate"></i>
                Atualizar Saldo
            </a>
        </div>
    </div>

</div>

@else
<script>
    window.location.href = '/login';
</script>
@endif

@endsection
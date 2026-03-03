@extends('layout')
@section('title', 'Reserva - Solicitações')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('conteudo')

<div class="col s12 m6 offset-m3">
    @if ($message = Session::get('success'))
    <div class="card green darken-1">
        <div class="card-content white-text">
            <span class="card-title">Usuário Autenticado</span>
            <p>Conta criada com sucesso! <br>PRAZO DE PEDIDO PARA VIAGEM:
                PASSAGEM AEREA: MININMO 10 DIAS DE ANTECENDENCIA! <br>
                PASSAGEM RODOVIARIA: MINIMO 5 DIAS DE ANTECEDENCIA! <br>
                VEICULO LEVE: MINIMO 5 DIAS DE ANTECEDENCIA! <br>
                HOSPEDAGEM: MINIMO 5 DIAS DE ANTECEDENCIA! <br>
            </p>
        </div>
    </div>
    @endif

    @if ($message = Session::get('success2'))
    <div class="card green darken-1 center">
        <div class="card-content white-text">
            <span class="card-title">Reserva solicitada com sucesso!</span>
            <p>Parabéns! A sua reserva foi solicitada com sucesso!<br>
                Acesse a aba "Minhas Reservas" para visualizar a sua solicitação.
            </p>
        </div>
    </div>
    @endif
</div>

@if (@auth()->user()->id != null)

<div class="container my-5" style="padding-top: 80px;">

    <div class="dashboard-grid">

        <a href="{{ route('reserva.passagem-aerea') }}" class="dashboard-card">
            <div class="icon">
                <i class="fa-solid fa-plane-departure"></i>
            </div>
            <div class="card-title">Reserva de Passagem</div>
            <div class="card-description">Aérea ou Rodoviária</div>
        </a>

        <a href="{{ route('reserva.veiculo') }}" class="dashboard-card">
            <div class="icon">
                <i class="fa-solid fa-car"></i>
            </div>
            <div class="card-title">Reserva de Veículo Leve</div>
        </a>

        <a href="{{ route('reserva.hospedagem') }}" class="dashboard-card">
            <div class="icon">
                <i class="fa-solid fa-hotel"></i>
            </div>
            <div class="card-title">Hotel/Hospedagem</div>
        </a>

        <a href="{{ route('reserva.adiantamento') }}" class="dashboard-card">
            <div class="icon">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div class="card-title">Adiantamento Viagem</div>
        </a>

    </div>

    <div class="rules-card">
        <div class="rules-header">
            <i class="fas fa-info-circle"></i> Política de Viagens e Reembolsos - Grupo Cargo Polo
        </div>

        <div class="rules-grid">
            <div>
                <div class="rules-section-title">PRAZOS DE PEDIDO</div>
                <table class="rules-table">
                    <tr><td>Passagem Aérea</td><td>Mínimo de <span class="highlight-yellow">10 dias</span> de antecedência</td></tr>
                    <tr><td>Passagem Rodoviária</td><td>Mínimo de <span class="highlight-yellow">5 dias</span> de antecedência</td></tr>
                    <tr><td>Veículo Leve</td><td>Mínimo de <span class="highlight-yellow">5 dias</span> de antecedência</td></tr>
                    <tr><td>Hospedagem</td><td>Mínimo de <span class="highlight-yellow">5 dias</span> de antecedência</td></tr>
                    <tr><td>Adiantamento</td><td>Mínimo de <span class="highlight-yellow">5 dias</span> (para viagens > 4 dias)</td></tr>
                </table>
            </div>

            <div>
                <div class="rules-section-title">VALORES DE REEMBOLSO</div>
                <table class="rules-table">
                    <tr><td>Café da Manhã</td><td>R$ 15,00/dia (se não incluso no hotel)</td></tr>
                    <tr><td>Almoço</td><td>R$ 45,00/dia (não cumulativo)</td></tr>
                    <tr><td>Jantar</td><td>R$ 45,00/dia (não cumulativo)</td></tr>
                </table>
                <p style="font-size: 0.75rem; margin-top: 10px; opacity: 0.8;">
                    * Não liberado para bebidas alcoólicas.
                </p>
            </div>
        </div>

        <div class="rules-footer">
            <strong>Observações importantes:</strong><br>
            • As viagens devem ser validadas pelo gerente da unidade.<br>
            • Solicitações devem ser feitas pela intranet.<br>
            • Reembolsos mediante apresentação de notas/cupons fiscais na plataforma <a href="{{route('rdv.index')}}"><span class="highlight-yellow">Rdv</span></a>.
        </div>
    </div>

</div>

@else
<script>
    alert('Você precisa estar logado para acessar essa página!');
    window.location.href = '/login';
</script>
@endif

<style>
    /* --- CONFIGURAÇÃO DO GRID --- */
    .dashboard-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) !important;
        gap: 30px !important;
    }

    /* --- ESTILO DOS CARDS (Estilo Branco/Clean) --- */
    .dashboard-card {
        background:  #fff !important;
        border-radius: 18px !important;
        padding: 30px 20px !important;
        text-align: center !important;
        text-decoration: none !important;
        box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important;
        transition: all 0.3s ease !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        height: 180px !important;
        position: relative !important;
        overflow: hidden !important;
        /* Borda transparente inicial para evitar pulo no hover */
        border: 2px solid transparent !important; 
    }

    /* --- ÍCONES (Font Awesome) --- */
    .dashboard-card .icon {
        font-size: 3rem !important;
        color: #003366 !important; /* Azul Escuro */
        margin-bottom: 15px !important;
        transition: all 0.3s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* --- HOVER EFFECTS --- */
    .dashboard-card:hover {
        transform: translateY(-8px) scale(1.03) !important;
        box-shadow: 0 12px 28px rgba(0,0,0,0.2) !important;
        border-color: #005bb6 !important; /* Borda fica azul */
    }

    .dashboard-card:hover .icon {
        color: #005bb6 !important; /* Ícone fica azul mais claro */
        transform: scale(1.1);
    }

    /* Efeito de Brilho Fundo */
    .dashboard-card::before {
        content: "" !important;
        position: absolute !important;
        top: -50% !important;
        left: -50% !important;
        width: 200% !important;
        height: 200% !important;
        background: radial-gradient(circle, rgba(0,91,182,0.05), transparent 60%) !important;
        transform: rotate(25deg) !important;
        opacity: 0 !important;
        transition: opacity 0.4s !important;
    }

    .dashboard-card:hover::before {
        opacity: 1 !important;
    }

    /* Textos dos Cards */
    .dashboard-card .card-title {
        font-size: 16px !important;
        font-weight: 600 !important;
        letter-spacing: 0.5px !important;
        color: #161616 !important; /* Texto escuro */
        z-index: 1;
    }

    .dashboard-card .card-description {
        font-size: 14px !important;
        color: #555 !important;
        margin-top: 5px !important;
        z-index: 1;
    }

    /* --- ESTILO DA TABELA DE REGRAS (Mantido Azul) --- */
    .rules-card {
        background: #fff !important;
        color: rgb(0, 0, 0);
        border-radius: 15px;
        padding: 25px;
        margin-top: 40px; /* Mais espaço entre os cards e as regras */
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .rules-header {
        border-bottom: 2px solid rgba(168, 168, 168, 0.4);
        margin-bottom: 20px;
        padding-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: bold;
    }

    .rules-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .rules-grid { grid-template-columns: 1fr; }
    }

    .rules-section-title {
        font-size: 1.1rem;
        margin-bottom: 15px;
        color: #003366;
        font-weight: 700;
    }

    .rules-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .rules-table td {
        padding: 10px;
        border-bottom: 1px solid rgb(168, 168, 168, 0.4);
    }

    .rules-footer {
        margin-top: 20px;
        font-size: 0.85rem;
        background: rgba(168, 168, 168, 0.1);
        padding: 15px;
        border-radius: 8px;
        line-height: 1.5;
    }

    .highlight-yellow { color: #003366; font-weight: bold; }
</style>

@endsection
@extends('layout')
@section('title', 'RDV - Cargo Polo')
@section('conteudo')


<div class="col s12 m6 offset-m3">
  <div class="container my-5">

    @if ($message = Session::get('success'))
    <div class="card green darken-1">
        <div class="card-content white-text">
        <span class="card-title">Sucesso!</span>
        <p>Parabéns! A solicitação foi realizada com sucesso!<br>
        </p>
        </div>
    </div>
    @endif

    {{-- for de 1 a 5 --}}
    @for ($i = 1; $i <= 5; $i++)
        @if ($message = Session::get('success'.$i))
            <div class="card green darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Sucesso!</span>
                    <p>Parabéns! A solicitação foi realizada com sucesso!<br>
                    </p>
                </div>
            </div>
        @endif
    @endfor


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

</div>
</div>

<div class="container my-5" style="padding-top: 80px;">
  
  <div class="dashboard-grid">

    <a href="{{ route('rdv.despesas')}}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-clipboard-list"></i>
      </div>
      <div class="card-title">Meus Relatórios</div>
      <div class="card-description">Resumo Geral dos Relatórios</div>
    </a>

    <a href="{{ route('rdv.despesas')}}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-utensils"></i>
      </div>
      <div class="card-title">Despesas</div>
      <div class="card-description">Criar uma despesa</div>
    </a>

    <a href="{{route('rdv.relatorio')}}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-list-check"></i>
      </div>
      <div class="card-title">Relatórios</div>
      <div class="card-description">Criar um relatório de despesa</div>
    </a>

    <a href="{{route('reserva.adiantamento')}}" class="dashboard-card">
      <div class="icon">
        {{-- <i class="fa-solid fa-suitcase-rolling"></i> --}}
        <i class="fa-solid fa-money-check-dollar"></i>
      </div>
      <div class="card-title">Adiantamentos</div>
      <div class="card-description">Solicitar um adiantamento</div>
    </a>

  </div>

<div class="rules-card">
    <div class="rules-header">
        <i class="fas fa-info-circle"></i> Reembolsos - Grupo Cargo Polo
    </div>

    <div class="rules-grid">

        <!-- 🔹 COLUNA 1 -->

        <div>
            <div class="rules-section-title">PASSO A PASSO PARA REEMBOLSO</div>
            <table class="rules-table">
                <tr><td><i>Clique aqui para abrir a  </i>                   <a href="/instrucao_despesas.pdf">
                    <span class="highlight-yellow">Instrução de Trabalho Despesas</span></a> </td></tr>
                <tr><td><i>Clique aqui para abrir a </i>                   <a href="/instrucao_adiantamento.pdf">
                    <span class="highlight-yellow">Instrução de Trabalho Adiantamentos</span></a> </td></tr>
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
        • Os relatórios devem ser validadas pelo gerente da unidade.<br>
        • Solicitações devem ser feitas pela intranet.<br>
        • Qualquer informação divergente será reprovada.<br>
        • Reembolsos mediante apresentação de notas fiscais na plataforma 
        <a href="{{route('rdv.index')}}"><span class="highlight-yellow">RDV</span></a>.
    </div>
</div>


<style>
  .dashboard-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) !important;
    gap: 30px !important;
  }

  .dashboard-card {
    background: #fff !important;
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
    /* Adicionei uma borda invisível inicial para o hover não "tremer" */
    border: 2px solid transparent !important; 
  }

  /* --- ESTILO DO ÍCONE (Font Awesome) --- */
  .dashboard-card .icon {
    font-size: 3rem !important;
    color: #003366 !important; /* Seu azul escuro */
    margin-bottom: 15px !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }

  /* --- HOVER DO CARD --- */
  .dashboard-card:hover {
    transform: translateY(-8px) scale(1.03) !important;
    box-shadow: 0 12px 28px rgba(0,0,0,0.2) !important;
    /* Aqui aplicamos a borda no card ou no ícone conforme sua preferência */
    border-color: #005bb6 !important; 
  }

  .dashboard-card:hover .icon {
    color: #005bb6 !important; /* Azul mais claro no hover */
    transform: scale(1.1); /* Efeito extra de pulso no ícone */
  }

  /* Brilho interno (efeito radial) */
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

  .dashboard-card .card-title {
    font-size: 16px !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    color: #161616 !important;
    z-index: 1; /* Garante que fique acima do efeito before */
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
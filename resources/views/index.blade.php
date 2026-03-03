@extends('layout')
@section('title', 'Central de Formulários')
@section('conteudo')

<div class="col s12 m6 offset-m3">

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

<div class="container my-5" style="padding-top: 80px;">
  
  <div class="dashboard-grid">

    <a href="{{ route('goto.route', ['route' => 'financeiro.index']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-hand-holding-dollar"></i>
      </div>
      <div class="card-title">Financeiro</div>
      <div class="card-description">Pagamentos à Vista</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'empresa.create']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-truck-field"></i>
      </div>
      <div class="card-title">Cadastro de Fornecedor</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'fiscal.index']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-file-invoice-dollar"></i>
      </div>
      <div class="card-title">Fiscal - Emissão NF</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'pagamento_pix.index']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-brands fa-pix"></i> </div>
      <div class="card-title">Combustivel - Pagamento Pix</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'florestal_pix.index']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-tree"></i>
      </div>
      <div class="card-title">Combustivel - Florestal Pix</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'saldo.index']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-gas-pump"></i>
      </div>
      <div class="card-title">Combustível - Saldo </div>
      <div class="card-description">Rede Frotas</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'saldo.valecard']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-wallet"></i>
      </div>
      <div class="card-title">Combustível - Saldo </div>
      <div class="card-description">ValeCard</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'produtos.create']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-boxes-stacked"></i>
      </div>
      <div class="card-title">Cadastro de Produtos</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'transf_veiculo.index']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-car-side"></i>
      </div>
      <div class="card-title">Transferência de Veículo</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'rdv.index']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-file-invoice"></i>
      </div>
      <div class="card-title">Rdv</div>
      <div class="card-description">Relatorio de Despesas</div>
    </a>

    {{-- <a href="javascript:void(0)" class="dashboard-card disabled-card" title="Funcionalidade em desenvolvimento">
      <span class="badge-construction">Em breve</span>
      
      <div class="icon">
        <i class="fa-solid fa-screwdriver-wrench"></i> </div>
      <div class="card-title">Rdv</div>
      <div class="card-description">Relatório de Despesas</div>
    </a> --}}

    <a href="{{route('dashboard')}}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-wrench"></i>
      </div>
      <div class="card-title">Portal Tempo de O.S.</div>
    </a>

    <a href="{{route('reserva.home')}}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-suitcase-rolling"></i>
      </div>
      <div class="card-title">Portal Reservas</div>
    </a>

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



/* DEIXAR O CARD EM CONSTRUÇÃO (DESABILITADO) */

/* Estilo para o Card Desabilitado */
.disabled-card {
    cursor: not-allowed !important; /* Mostra o ícone de "proibido" no mouse */
    opacity: 0.7 !important;        /* Deixa o card levemente opaco */
    filter: grayscale(80%) !important; /* Tira um pouco da cor para parecer inativo */
    pointer-events: none;           /* Desativa o clique do link */
    background: #f8f9fa !important; /* Fundo levemente mais cinza */
}

/* Selo (Badge) "Em breve" */
.badge-construction {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #ffc107; /* Amarelo de atenção */
    color: #000;
    font-size: 10px;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    z-index: 2;
}

/* Efeito sutil no ícone em construção */
.disabled-card .icon i {
    color: #6c757d !important; /* Cinza para o ícone */
}

/* Garante que o hover não cause o efeito de "subida" nos desabilitados */
.disabled-card:hover {
    transform: none !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important;
    border-color: transparent !important;
}



</style>

@endsection

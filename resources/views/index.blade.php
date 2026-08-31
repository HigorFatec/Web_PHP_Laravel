@extends('layout')
@section('title', 'Central de Formulários')
@section('conteudo')

@if (@auth()->user()->id != null)

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



{{-- <div class="container" style="margin-top: 30px; margin-bottom: 20px;">
  <!-- O fundo continua o azul e o design original do seu sistema -->
  <div class="anniversary-banner" style="border: 2px solid rgba(255,193,7,0.4); background: linear-gradient(135deg, #003366 0%, #005bb6 100%) !important;">
    <div class="anniversary-content">
      <div class="icon-box" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 50%;">
        <!-- Ícone de Troféu em Dourado para destacar a Copa -->
        <i class="fa-solid fa-trophy" style="font-size: 1.8rem; color: #ffc107 !important;"></i>
      </div>
      <div style="display: flex; flex-direction: column;">
          <span style="font-size: 1.2rem; color: #fff;">Central de Formulários <strong style="color: #ffc107;">- Clima de Copa!</strong></span>
        <small style="opacity: 0.8; font-style: italic; color: #fff;">Grupo Cargo Polo: Jogando juntos pelo melhor resultado.</small>
      </div>
    </div>
  </div>
</div> --}}


</div>

<div class="container my-5" style="padding-top: 100px;">
  
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

    <a href="{{ route('goto.route', ['route' => 'saldo.combustivel']) }}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-gas-pump"></i>
      </div>
      <div class="card-title">Combustível </div>
      <div class="card-description">Pix e Saldo</div>
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
        <i class="fa-brands fa-avianex"></i>
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

    <a href="{{route('implantacao_saldo.menu')}}" class="dashboard-card">
      <div class="icon">
        <i class="fa-solid fa-cubes"></i>
      </div>
      <div class="card-title">Estoque Almoxarifado</div>
      <div class="card-description">Ajuste de Estoque</div>
    </a>

    <a href="{{route('ajuda_de_custo.index')}}" class="dashboard-card">
      <div class="icon">
        <i class="fa-brands fa-pix"></i>
      </div>
      <div class="card-title">Ajuda de Custo</div>
      <div class="card-description">Solicitar Ajuda de Custo</div>
    </a>


        {{-- <a href="javascript:void(0)" class="dashboard-card disabled-card" title="Funcionalidade em desenvolvimento">
      <span class="badge-construction">Em breve</span>
      
      <div class="icon">
        <i class="fa-solid fa-screwdriver-wrench"></i> </div>
      <div class="card-title">Ajuste de Estoque</div>
      <div class="card-description">Implantação de Saldo</div>
    </a> --}}
    
        {{-- <a href="javascript:void(0)" class="dashboard-card disabled-card" title="Funcionalidade em desenvolvimento">
      <span class="badge-construction">Em breve</span>
      
      <div class="icon">
        <i class="fa-solid fa-screwdriver-wrench"></i> </div>
      <div class="card-title">Ajuda de Custo</div>
      <div class="card-description">Solicitar Ajuda de Custo Proporcional</div>
    </a> --}}


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





<style>
.anniversary-banner {
    background: linear-gradient(135deg, #003366 0%, #005bb6 100%) !important;
    border-radius: 18px !important;
    padding: 15px 25px !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(0,51,102,0.3) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    animation: fadeInDown 0.8s ease-out !important;
}

.anniversary-content {
    display: flex !important;
    align-items: center !important;
    gap: 15px !important;
    font-size: 1.1rem !important;
}

.anniversary-content i {
    font-size: 1.8rem !important;
    color: #ffc107 !important; /* Destaque em dourado para a celebração */
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
.highlight-years {
    background: linear-gradient(to bottom, #fff 20%, #ffc107 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 1.4rem;
    font-weight: 800;
    filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.3));
}
</style>
<style>
/* Adicione isso ao seu bloco de style */
.anniversary-banner {
    position: relative;
    overflow: hidden;
}

.anniversary-banner::before, .anniversary-banner::after {
    content: "✨";
    position: absolute;
    font-size: 1.5rem;
    opacity: 0.6;
    animation: float 3s ease-in-out infinite;
}

.anniversary-banner::before { left: 10px; top: 10px; }
.anniversary-banner::after { right: 10px; bottom: 10px; animation-delay: 1.5s; }

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(20deg); }
}
</style>








@else
<script>
    window.location.href = '/login';
</script>
@endif

@endsection

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
        <img src="{{ asset('img/financeiro.png') }}" alt="Financeiro">
      </div>
      <div class="card-title">Financeiro</div>
      <div class="card-description">Pagamentos à Vista</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'empresa.create']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/fornecedor.png') }}" alt="Cadastro de Fornecedor">
      </div>
      <div class="card-title">Cadastro de Fornecedor</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'fiscal.index']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/fiscal.png') }}" alt="Fiscal">
      </div>
      <div class="card-title">Fiscal - Emissão NF</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'pagamento_pix.index']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/pagamento_pix.png') }}" alt="Pagamento Pix">
      </div>
      <div class="card-title">Combustivel - Pagamento Pix</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'florestal_pix.index']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/florestal_pix.png') }}" alt="Florestal Pix">
      </div>
      <div class="card-title">Combustivel - Florestal Pix</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'saldo.index']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/saldo.png') }}" alt="Saldo">
      </div>
      <div class="card-title">Combustível - Saldo </div>
      <div class="card-description">Rede Frotas</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'saldo.valecard']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/money.png') }}" alt="Saldo">
      </div>
      <div class="card-title">Combustível - Saldo </div>
      <div class="card-description">ValeCard</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'produtos.create']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/produtos.png') }}" alt="Cadastro de Produtos">
      </div>
      <div class="card-title">Cadastro de Produtos</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'transf_veiculo.index']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/placa.png') }}" alt="Transferência de Veículo">
      </div>
      <div class="card-title">Transferência de Veículo</div>
    </a>

    <a href="{{ route('goto.route', ['route' => 'sinistro.index']) }}" class="dashboard-card">
      <div class="icon">
        <img src="{{ asset('img/sinistro.png') }}" alt="Sinistro">
      </div>
      <div class="card-title">Sinistro</div>
    </a>

    <a href="{{route('dashboard')}}" class="dashboard-card">
      <div class="icon">
        <img src="{{asset('img/mechanic.png')}}" alt="Tempo de Manutenção de Serviço">
      </div>
      <div class="card-title">Portal Tempo de O.S.</div>
    </a>

    <a href="{{route('reserva.home')}}" class="dashboard-card">
      <div class="icon">
        <img src="{{asset('img/travel-agent.png')}}" alt="Portal de Viagens">
      </div>
      <div class="card-title">Portal Reservas</div>
    </a>

  </div>
</div>

<style>
  body {
    background: #f4f6f9 !important;
    font-family: 'Segoe UI', Roboto, Arial, sans-serif !important;
  }

  .dashboard-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) !important;
    gap: 30px !important;
  }

  .dashboard-card {
    background: linear-gradient(135deg, #0055aa, #003366) !important;
    border-radius: 18px !important;
    padding: 30px 20px !important;
    text-align: center !important;
    color: #fff !important;
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
  }

  .dashboard-card::before {
    content: "" !important;
    position: absolute !important;
    top: -50% !important;
    left: -50% !important;
    width: 200% !important;
    height: 200% !important;
    background: radial-gradient(circle, rgba(255,255,255,0.15), transparent 60%) !important;
    transform: rotate(25deg) !important;
    opacity: 0 !important;
    transition: opacity 0.4s !important;
  }

  .dashboard-card:hover::before {
    opacity: 1 !important;
  }

  .dashboard-card:hover {
    transform: translateY(-8px) scale(1.03) !important;
    box-shadow: 0 12px 28px rgba(0,0,0,0.2) !important;
  }

    .dashboard-card .icon img {
        max-width: 70px !important;
        margin-bottom: 15px !important;
        /* ❌ remova essa linha abaixo */
        /* filter: brightness(0) invert(1) !important; */
    }

    

  .dashboard-card .card-title {
    font-size: 16px !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
  }
</style>
@endsection

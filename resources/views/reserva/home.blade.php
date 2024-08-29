@extends('layout')
@section('title', 'Reserva - Solicitações')
@section('conteudo')

<br>
    
<div class="row container">
      
  @if ($message = Session::get('success'))
  <div class="card green darken-1">
    <div class="card-content white-text">
      <span class="card-title">Usuário Autenticado</span>
      <p>Conta criada com sucesso! <br>PRAZO DE PEDIDO PARA VIAGEM:
        PASSAGEM AEREA: 	MININMO 10 DIAS DE ANTECENDENCIA! <br>
        PASSAGEM RODOVIARIA: 	MINIMO 5 DIAS DE ANTECEDENCIA! <br>
        VEICULO LEVE: 		MINIMO 5 DIAS DE ANTECEDENCIA! <br>
        HOSPEDAGEM:		MINIMO 5 DIAS DE ANTECEDENCIA! <br>
     </p>
    </div>
  </div>
  @endif
  @if ($message = Session::get('success2'))
  <div class="card green darken-1">
    <div class="card-content white-text">
      <span class="card-title">Passagem Solicitada com Sucesso!</span>
      <p>Parabéns! A sua reserva foi solicitada com sucesso!<br>
         Acesse a aba "Minhas Reservas" para visualizar a sua solicitação.
     </p>
    </div>
  </div>
  @endif

  @if (@auth()->user()->id != null)
      

    <div class="col s12 m4">
        <div class="card">
          <span class="card-title" style="color: red"><center><b>Reserva de Passagem</b></center></span>
            <div class="card-image">
              <img src="{{asset('img/aviao.png')}}" class="custom-image">
              <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route("reserva.passagem-aerea")}}"><i class="material-icons">add</i></a>
            </div>
            <div class="card-content">
              <p>Reserve a sua passagem Áerea ou Rodoviária</p>
            </div>
          </div>
    </div>


    <div class="col s12 m4">
        <div class="card">
          <span class="card-title" style="color: red"><center><b>Reserva de Veiculo Leve</b></center></span>
            <div class="card-image">
              <img src="{{asset('img/veiculo.png')}}" class="custom-image">
              <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route('reserva.veiculo')}}"><i class="material-icons">add</i></a>
            </div>
            <div class="card-content">
              <p>Reserve o seu veículo leve</p>
            </div>
          </div>
    </div>

    <div class="col s12 m4">
        <div class="card">
          <span class="card-title" style="color: red"><center><b>Hotel/Hospedagem</b></center></span>
            <div class="card-image">
              <img src="{{asset('img/hotel.png')}}" class="custom-image">
              <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route('reserva.hospedagem')}}"><i class="material-icons">add</i></a>
            </div>
            <div class="card-content">
              <p>Reserve a sua locação</p>
            </div>
          </div>
    </div>

    <div class="col s12 m4">
        <div class="card">
          <span class="card-title" style="color: red"><center><b>Adiantamento Viagem</b></center></span>
            <div class="card-image">
              <img src="{{asset('img/money.webp')}}" class="custom-image">
              <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route('reserva.adiantamento')}}"><i class="material-icons">add</i></a>
            </div>
            <div class="card-content">
              <p>Faça o seu adiantamento de viagem</p>
            </div>
          </div>
    </div>

    

    <div class="card-image">
      <img src="{{asset('img/regras.jpg')}}" class="custom-image2">
    </div>

  </div>

  @else
  <script>
      alert('Você precisa estar logado para acessar essa página!');
  window.location.href = '/login';
  </script>
  

  @endif
          

@endsection
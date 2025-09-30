@extends('layout')
@section('title', 'Reserva - Solicitações')
@section('conteudo')
    
<div class="row">
      
  @if ($message = Session::get('success'))
  <div class="col s12 m6 offset-m3">

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
  <div class="card green darken-1 center">
    <div class="card-content white-text">
      <span class="card-title">Reserva solicitada com sucesso!</span>
      <p>Parabéns! A sua reserva foi solicitada com sucesso!<br>
         Acesse a aba "Minhas Reservas" para visualizar a sua solicitação.
     </p>
    </div>
  </div>

  </div>
  @endif

  @if (@auth()->user()->id != null)


   <div class="cards-container">
 <div class="card rounded-card" style="padding-bottom: 30px;">
    <span class="card-title" style="color: black">
      <center><b><h6>Reserva de Passagem</h6></b></center>
    </span>
    <div class="card-image">
      <img src="{{asset('img/destino.png')}}" class="custom-image">
      <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route("reserva.passagem-aerea")}}">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div>

  <div class="card rounded-card" style="padding-bottom: 30px;">
    <span class="card-title" style="color: black">
      <center><b><h6>Reserva de Veículo Leve</h6></b></center>
    </span>
    <div class="card-image">
      <img src="{{asset('img/veiculo_reserva.png')}}" class="custom-image">
      <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route('reserva.veiculo')}}">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div>

  <div class="card rounded-card" style="padding-bottom: 30px;">
    <span class="card-title" style="color: black">
      <center><b><h6>Hotel/Hospedagem</h6></b></center>
    </span>
    <div class="card-image">
      <img src="{{asset('img/hotel_reserva.png')}}" class="custom-image">
      <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route('reserva.hospedagem')}}">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div>

  <div class="card rounded-card" style="padding-bottom: 30px;">
    <span class="card-title" style="color: black">
      <center><b><h6>Adiantamento Viagem</h6></b></center>
    </span>
    <div class="card-image">
      <img src="{{asset('img/adiantamento_reserva.png')}}" class="custom-image">
      <a class="btn-floating halfway-fab waves-effect waves-light red" href="{{route('reserva.adiantamento')}}">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div>
</div>


  <div class="row center">
      <div class="card-image">
      <img src="{{asset('img/regras3.jpg')}}" class="custom-image2 rounded-card">
    </div>
  </div>



  </div>

  @else
  <script>
      alert('Você precisa estar logado para acessar essa página!');
  window.location.href = '/login';
  </script>
  

  @endif
          

@endsection
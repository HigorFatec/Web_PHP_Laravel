@extends('layout')
@section('title', 'Adiantamento de Viagem')
@section('conteudo')

<br>

<div class="row container">

  @if ($message = Session::get('error_dias'))
  <div class="card red darken-1">
    <div class="card-content white-text">
      <span class="card-title">Erro!</span>
      <p>Para fazer uma solicitação de adiantamento de viagem<br>
         É necessário 5 dias ou mais de viagem!
     </p>
    </div>
  </div>
  @endif


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


<div class="card">
  <div class="card-content">
      <span class="card-title center"><b>Adiantamento de Viagem</b></span><br>

<form action="/reserva/adiantamento" method="POST" enctype="multipart/form-data">
    @csrf
          

    <input type="text" name="destino" placeholder="Destino" required><br><br>


    Data de inicio de viagem:
    <input type="date" name="ida" placeholder="Data" required><br><br>
    Data do fim da viagem:
    <input type="date" name="volta" placeholder="Data" required>

    
    <input type="text" name="motivo" placeholder="Motivo da Viagem" required>
    <input type="text" name="validacao" placeholder="Validado pelo Gestor (autorização)" required>
    <input type="email" name="email_gestor" placeholder="Email do Gestor" required>
    <input type="text" name="observacoes" placeholder="Observações (não necessariamente)"><br><br>



    <span class="card-title center"><b>Dados do viajante</b></span>
    <input type="text" name="nome" placeholder="Nome Completo" required>
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="number" name="cpf" placeholder="CPF" required>
    <input type="number" name="rg" placeholder="RG" required><br><br>
    Data de Nascimento:
    <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required><br><br>

    <span class="card-title center"><b>Dados Bancários</b></span>
    <input type="text" name="banco" placeholder="Banco" required>
    <input type="number" name="agencia" placeholder="Agência" required>
    <input type="number" name="conta" placeholder="Conta" required>
    <input type="text" name="tipo_conta" placeholder="Tipo de Conta" required>
    <input type="text" name="titular" placeholder="Titular da Conta" required>
    <input type="text" name="pix" placeholder="PIX" required><br><br>

    Anexar Documento(CNH ou RG):<br>
    <input type="file" name="foto" id="foto" accept="image/*"><br><br>


    <a href="{{route('reserva.home')}}">
      <button type="button" class="btn-cadastrar left">Voltar</button></a>
    <!-- Outros campos aqui -->
    <button type="submit" class="btn-cadastrar right">Enviar</button><br><br>
  </form>
</div>
</div>

</div>


@endsection
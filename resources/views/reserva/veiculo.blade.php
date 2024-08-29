@extends('layout')
@section('title', 'Veiculo Leve')
@section('conteudo')

<br>

<div class="row container">


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
      <span class="card-title center"><b>Reserva de Veiculo Leve</b></span><br>

<form action="/reserva/veiculo" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="text" name="origem" placeholder="Local de Retirada" required>
    <input type="text" name="destino" placeholder="Local de Devolução" required><br><br>


    Data de Retirada
    <input type="date" name="ida" placeholder="Data" required><br><br>
    Data de Devolução:
    <input type="date" name="volta" placeholder="Data" required>

    <input type="text" name="periodo" placeholder="Periodo de Devolução (EX: DIA 5, DE MANHÃ/TARDE/NOITE):">

    
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
    <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required>

    Anexar Documento(CNH ou RG):<br>
    <input type="file" name="imagem" id="imagem" accept="image/*"><br><br>

    <a href="{{route('reserva.home')}}">
      <button type="button" class="btn-cadastrar left">Voltar</button></a>
    <!-- Outros campos aqui -->
    <button type="submit" class="btn-cadastrar right">Enviar</button><br><br>
  </form>
</div>
</div>

</div>


@endsection
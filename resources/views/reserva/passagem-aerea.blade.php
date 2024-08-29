@extends('layout')
@section('title', 'Passagem Áerea')
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
      <span class="card-title center"><b>Reserva de Passagem</b></span><br>
      <span class="card-title center"><b>Selecione o tipo de Passagem:</b></span>

<form action="/reserva/passagem-aerea" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="btn-group center" role="group" aria-label="Tipo de Reserva">
        <input type="hidden" name="tipo" id="tipo" required>
        <button type="button" class="btn" data-value="aerea">Aérea</button>
        <button type="button" class="btn" data-value="rodoviaria">Rodoviária</button>
    </div><br>
    
          

    <input type="text" name="origem" placeholder="Origem" required>
    <input type="text" name="destino" placeholder="Destino" required><br><br>


    Data de ida
    <input type="date" name="ida" placeholder="Data" required><br><br>
    Data de volta(Não necessariamente):
    <input type="date" name="volta" placeholder="Data">
    <input type="text" name="motivo" placeholder="Motivo da Viagem" required>
    <input type="text" name="validacao" placeholder="Validado pelo Gestor (autorização)" required>
    <input type="email" name="email_gestor" placeholder="Email do Gestor" required>
    <input type="text" name="observacoes" placeholder="Observações (não necessariamente)"><br><br>



    <span class="card-title center"><b>Dados do viajante</b></span>
    <input type="text" name="nome" placeholder="Nome Completo" required>
    <input type="number" name="cpf" placeholder="CPF" required>
    <input type="number" name="rg" placeholder="RG" required><br><br>
    Data de Nascimento:
    <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required>
    <input type="email" name="email" placeholder="E-mail" required><br><br>

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

<script>
  document.querySelectorAll('.btn-group .btn').forEach(button => {
      button.addEventListener('click', function() {
          // Remove a classe active de todos os botões
          document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
          
          // Adiciona a classe active ao botão clicado
          this.classList.add('active');
          
          // Atualiza o valor do campo hidden
          document.getElementById('tipo').value = this.getAttribute('data-value');
      });
  });
  </script>
  


@endsection
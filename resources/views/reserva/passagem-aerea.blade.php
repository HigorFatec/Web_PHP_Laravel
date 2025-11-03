@extends('layout')
@section('title', 'Passagem Áerea')
@section('conteudo')

@php $user = auth()->user(); @endphp

<div class="row">


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

<div class="container">
<div class="card">
  <div class="card-content">
      <span class="card-title center"><b>Reserva de Passagem</b></span><br>

  <form action="/reserva/passagem-aerea" method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf

          <div class="btn-group center" role="group" aria-label="Tipo de Viajante">
        <input type="hidden" name="tipo_viajante" id="tipo_viajante" required>
        <button type="button" class="btn" data-value="viajante">Sou o Viajante</button>
        <button type="button" class="btn" data-value="nao-viajante">Não Sou o Viajante</button>


    </div><br>
    <br>

    <div id="campos-viajante" class="tipo-campos" style="display:none;">


      <span class="card-title center"><b>Dados do viajante</b></span>
      <input type="text" name="nome" placeholder="Nome Completo" required value = "{{ $user->name ?? ''}}" readonly>
      <input type="number" name="cpf" placeholder="CPF" required value = "{{ $user->cpf ?? ''}}" readonly>

      <input type="email" name="email" placeholder="E-mail" required value = "{{ $user->email ?? ''}}" readonly><br>

      <input type="text" name="filial_viajante" placeholder="Filial do viajante" required value = "{{ $user->filial ?? ''}}" readonly> <br>

      <input type="number" name="rg" placeholder="RG" required value = "{{ $user->rg ?? '' }}"><br>
      Data de Nascimento:
      <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required value = "{{ $user->data_nascimento ?? ''}}">

      <input type="email" name="email_gestor" placeholder="Email do Gestor" required value = "{{ $user->email_gestor ?? ''}}" readonly><br><br><br>

    </div>

    <div id="campos-nao-viajante" class="tipo-campos" style="display:none;">


      <span class="card-title center"><b>Dados do viajante</b></span>
      <input type="text" name="nome" placeholder="Nome Completo" required>
      <input type="number" name="cpf" placeholder="CPF" required>
      <input type="number" name="rg" placeholder="RG" required><br><br>
      Data de Nascimento:
      <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required>
      <input type="email" name="email" placeholder="E-mail" required><br>
      
      <input type="text" name="filial_viajante" placeholder="Filial do viajante" required> <br>

      Gestor Aprovador: <br>
    <select name="email_gestor" id="email_gestor" required>

        <option value=" "></option>
        @foreach ($aprovadores as $aprovador)
            <option value="{{$aprovador->email}}">{{$aprovador->operacao}} - {{$aprovador->nome}}</option>
        @endforeach

    </select> <br>

    </div>

  


    Selecione o tipo de Passagem: <br>
    <select name="tipo" id="tipo" required>

        <option value=" "></option>
        <option value="aerea">Aérea</option>
        <option value="rodoviaria">Rodoviária</option>

    </select> <br>
    

    <input type="text" name="origem" placeholder="Origem" required>
    <input type="text" name="destino" placeholder="Destino" required><br><br>


    Data de ida
    <input type="date" name="ida" placeholder="Data" required><br><br>
    Data de volta (Não obrigatório):
    <input type="date" name="volta" placeholder="Data">

    <input type="text" name="embarque" placeholder="Pretenção de Horário para Embarque" required>

    <input type="text" name="motivo" placeholder="Motivo da Viagem" required>
    <input type="text" name="validacao" placeholder="Validado pelo Gestor (autorização)" required>
    <input type="text" name="observacoes" placeholder="Observações (não necessariamente)"><br><br>




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





{{-- SCRIPT DO FISCAL --}}
<script>
  document.querySelectorAll('.btn-group .btn').forEach(button => {
      button.addEventListener('click', function() {
          // Remove a classe active de todos os botões
          document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
          
          // Adiciona a classe active ao botão clicado
          this.classList.add('active');
          
          // Atualiza o valor do campo hidden
          document.getElementById('tipo_viajante').value = this.getAttribute('data-value');
      });
  });
  </script>
  
<script>
function validarFormulario() {
  const tipo = document.getElementById('tipo_viajante').value;

  // 1️⃣  Primeiro, desativa o required de todos os blocos escondidos
  document.querySelectorAll('.tipo-campos').forEach(div => {
    if (div.style.display === 'none') {
      div.querySelectorAll('[required]').forEach(el => {
        el.dataset.tmpRequired = "1";     // guarda info p/ restaurar se precisar
        el.removeAttribute('required');
      });
    }
  });

  // 2️⃣  Validação extra que você já tem (exemplo do campo foto)
  const foto = document.getElementById('foto');
  if (tipo === 'viajante' && (!foto || foto.files.length === 0)) {
    alert('O campo "Documento" é obrigatório para o tipo "' + tipo + '".');
    foto.focus();
    return false;
  }

  // 3️⃣  Se chegou aqui, deixa o navegador validar normalmente os visíveis
  return true;
}
</script>

    
  
<script>
    const buttons = document.querySelectorAll('.btn-group .btn');
    const tipoInput = document.getElementById('tipo');
  
    const camposDevolucao = document.getElementById('campos-viajante');
    const camposVenda = document.getElementById('campos-nao-viajante');

  
    function esconderTodosCampos() {
      document.querySelectorAll('.tipo-campos').forEach(div => {
        div.style.display = 'none';
  
        // Desabilita todos inputs, selects e textareas dentro da div
        div.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
      });
    }
  
    function habilitarCampos(div) {
      div.style.display = 'block';
  
      // Habilita inputs, selects e textareas da div visível
      div.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
    }
  
    buttons.forEach(button => {
      button.addEventListener('click', () => {
        const valor = button.getAttribute('data-value');
        tipoInput.value = valor;
  
        esconderTodosCampos();
  
        if (valor === 'viajante') {
          habilitarCampos(camposDevolucao);
        } else if (valor === 'nao-viajante') {
          habilitarCampos(camposVenda);
        } 
      });
    });
  
    // Opcional: ao carregar a página, esconder e desabilitar tudo
    window.addEventListener('load', () => {
      esconderTodosCampos();
    });
  </script>
  


@endsection
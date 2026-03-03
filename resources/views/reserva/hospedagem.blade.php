@extends('layout')
@section('title', 'Hospedagem')
@section('conteudo')


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
      <span class="card-title center"><b>Hospedagem/Hotel</b></span><br>

      <div style="text-align: center; margin-bottom: 25px;">
                <a href="https://cargopolo.sharepoint.com/:x:/s/Hospedagem/IQCLbSAVHf0EQJCzqZr8eb-KAfcMgzYNaS0GXv37MKPOszI" target="_blank" 
                   style="background: linear-gradient(135deg, #0055aa, #003366); color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px;">
                    🏨 Ver Lista de Sugestões de Hotéis (SharePoint)
          </a>
      </div>

<form action="/reserva/hospedagem" method="POST" enctype="multipart/form-data" onsubmit="return disableButtonOnClick(this.querySelector('button[type=submit]'));">
    @csrf

    Cidade de Hospedagem:
      <select name="destino" id="cidade_select" class="browser-default" required>
        <option value = ""></option>
          @foreach ($cidades as $c)
              <option value="{{ $c->DESCRI }}">{{ $c->DESCRI }} - {{ $c->ESTADO }}</option>
          @endforeach
      </select><br><br>



    Data de Check-In
    <input type="date" name="ida" placeholder="Data" required><br><br>
    Data de Check-Out:
    <input type="date" name="volta" placeholder="Data" required>

    <input type="text" name="periodo" placeholder="Periodo (EX: DIA 5, DE MANHÃ/TARDE/NOITE):">

    
    <input type="text" name="motivo" placeholder="Motivo da Viagem" required>

    <input type="text" name="referencia" placeholder="Hotel Próximo a: (Ponto de Referência):" required>
    

    <input type="text" name="validacao" placeholder="Validado pelo Gestor (autorização)" required>

    
      Gestor Aprovador: <br>
    <select name="email_gestor" id="email_gestor" required>

        <option value=" "></option>
        @foreach ($aprovadores as $aprovador)
            <option value="{{$aprovador->email}}">{{$aprovador->operacao}} - {{$aprovador->nome}}</option>
        @endforeach

    </select> <br>
    
    
    <input type="text" name="observacoes" placeholder="Observações (não necessariamente)"><br><br>



    <span class="card-title center"><b>Dados do viajante</b></span>
    <input type="text" name="nome" placeholder="Nome Completo" required>
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="number" name="cpf" placeholder="CPF" required>
    <input type="number" name="rg" placeholder="RG" required><br><br>
    Data de Nascimento:
    <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required><br>
    
    Filial do Viajante: <br>
    <select name="filial_viajante" id="filial" required>

        <option value=" "></option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach

    </select> <br>

    
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
    $(document).ready(function() {
        $('#cidade_select').select2({
            placeholder: 'Selecione ou pesquise a cidade',
            width: '100%'
        });
    });
    </script>

@endsection
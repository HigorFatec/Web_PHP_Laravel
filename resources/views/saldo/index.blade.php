@extends('layout')
@section('title','Saldo de Combustível Rede Frota')
@section('conteudo')

@if (@auth()->user()->id != null)


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0 !important;
            color: #333;
        }


        /* .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
        } */
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .saldo {
            font-size: 1.5em;
            color: #27ae60;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .data {
            text-align: center;
            font-size: 0.95em;
            color: #888;
        }
        .empty {
            text-align: center;
            color: #e74c3c;
            font-weight: bold;
        }

    </style>
</head>
<body>
  
<center>
  <div class="row center">
    <div class="col s12 m6 offset-m3">
      <div class="card white darken-1">
                  <br><span class="card-title center"><b>Saldo - Rede Frota</b></span> <br>

        <div class="card-content white-text">

          <span class="saldo"><b> {{ $saldo->valor }} </b></span>
          <div class="data"><center>Atualizado em: {{ \Carbon\Carbon::parse($saldo->data_insercao)->format('d/m/Y H:i') }} </center></div>
        </div>
        <div class="card-action">
          <a href="/saldo">Atualizar</a>
        </div>
      </div>
    </div>
  </div>
</center>

</body>
</html>

@else
<script>
    window.location.href = '/login';
</script>
@endif

@endsection
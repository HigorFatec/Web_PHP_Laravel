@extends('layout')
@section('title', 'Reprovar Solicitação')
@section('conteudo')

<div class="row">
    <div class="col s12 m6 offset-m3">


    <span class="card-title center">Reprovar Adiantamento</span>

    <p><strong>Viajante:</strong> {{ $adiantamento->nome }}</p>
    <p><strong>Valor:</strong> {{ $adiantamento->valor }}</p>

    <form method="POST" action="{{ route('adiantamento.reprovar', $adiantamento->approval_token) }}">
        @csrf

        <label>Motivo da reprovação</label><br>
        <textarea name="motivo" rows="5" style="width:100%" required></textarea>

        <br><br>

        <center><button class="btn" type="submit" name="action">Confirmar reprovação
            <i class="material-icons right">send</i>
          </button></center><br>

    </form>

    </div>
</div>



@endsection
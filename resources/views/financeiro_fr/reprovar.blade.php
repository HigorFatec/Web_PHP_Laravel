@extends('layout')
@section('title', 'Reprovar Solicitação')
@section('conteudo')

<div class="row">
    <div class="col s12 m6 offset-m3">


    <span class="card-title center">Reprovar Solicitação</span>

    <p><strong>Solicitante:</strong> {{ $r->solicitante }}</p>
    <p><strong>Valor:</strong> {{ $r->valor }}</p>

    <form action="{{ route('financeiro.reprovar_financeiro', $r->id) }}" method="POST" onsubmit="return confirm('Deseja realmente reprovar?')">

        @csrf

        <label>Motivo da reprovação</label><br>
        <textarea name="motivo_reprovacao" rows="5" style="width:100%" required></textarea>

        <br><br>

        <center><button class="btn" type="submit" name="action">Confirmar reprovação
            <i class="material-icons right">send</i>
          </button></center><br>

    </form>

    </div>
</div>



@endsection
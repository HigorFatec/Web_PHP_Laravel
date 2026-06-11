@extends('layout')
@section('conteudo')
<div class="container center" style="margin-top: 50px;">
    <div class="card">
        <div class="card-content">
            <i class="material-icons large {{ $tipo == 'success' ? 'green-text' : 'red-text' }}">
                {{ $tipo == 'success' ? 'check_circle' : 'cancel' }}
            </i>
            <h4>{{ $mensagem }}</h4>
            <p>Você já pode fechar esta aba.</p>
            <br>
            <a href="/" class="btn blue">Voltar ao Sistema</a>
        </div>
    </div>
</div>
@endsection
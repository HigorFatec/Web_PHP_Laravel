@extends('layout')
@section('title', 'Redefinir Senha')
@section('conteudo')
<div class="row">
    <div class="col s12 m6 offset-m3">
        <h4>Redefinir Senha</h4>
        <p>Digite seu e-mail para receber o link de redefinição de senha.</p>

@if (session('status'))
    <div>{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label for="email">Email:</label>
    <input type="email" name="email" required>
    <button type="submit">Enviar link de redefi
        nição</button>
</form>
</div></div>

@endsection
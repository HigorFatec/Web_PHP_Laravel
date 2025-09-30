@extends('layout')
@section('title', 'Redefinir Senha')
@section('conteudo')
<div class="row">
    <div class="col s12 m6 offset-m3">

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <label>Nova senha:</label>
    <input type="password" name="password" required>

    <label>Confirme a nova senha:</label>
    <input type="password" name="password_confirmation" required>

    <button type="submit">Redefinir</button>
</form>
</div></div>

@endsection
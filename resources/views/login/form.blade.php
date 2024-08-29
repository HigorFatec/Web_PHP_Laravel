@extends('layout')
@section('title', 'Reserva - Solicitações')
@section('conteudo')

<br>

@auth
<script>window.location = "/reserva";</script>

@else


<div class="row">


    <div class="col s12 m6 offset-m3">

    {{-- Mensaguem de sucesso ao logouff--}}
    @if ($message = Session::get('success'))
    <div class="card green darken-1">
    <div class="card-content white-text">
        <span class="card-title">Logouff</span>
        <p>Logouff realizado com sucesso!!
    </p>
    </div>
    </div>
    @endif

    @if($mensagem = Session::get('erro'))
    <div class="card red darken-1">
        <div class="card-content white-text">
            <span class="card-title">Erro</span>
            <p>{{$mensagem}}
        </p>
        </div>
        </div>
    @endif


        
        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Login</b></span>



@if($errors->any())
    @foreach($errors->all() as $error)
        {{$error}} <br>
    @endforeach
@endif


<form action="{{route('login.auth')}}"method="POST">
    @csrf
    Email: <br> <input type="text" name="email"> <br>
    Senha: <br> <input type="password" name="password"> <br>
    <center>
        <label>
            <input type="checkbox" name="remember" />
            <span>Lembrar-me</span>
        </label> <br>

<button type="submit" class="btn-cadastrar right">Entrar</button>

<a href="{{route('login.create')}}">
    <button type="button" class="btn-cadastrar left">Cadastrar</button></a><br></center>

</form>
 
@endauth

@endsection
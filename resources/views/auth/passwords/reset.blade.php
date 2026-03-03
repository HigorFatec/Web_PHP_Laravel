@extends('layout')
@section('title', 'Redefinir Senha - CargoPolo')

@section('conteudo')
<div class="login-wrapper">
    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l4 offset-l4">

                {{-- Alertas de Feedback --}}
                @if ($message = Session::get('success'))
                    <div class="card-panel green lighten-4 green-text text-darken-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-check"></i> {{ $message }}
                    </div>
                @endif

                @if ($message = Session::get('erro'))
                    <div class="card-panel red lighten-4 red-text text-darken-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-xmark"></i> {{ $message }}
                    </div>
                @endif

                <div class="card card-login">
                    <div class="card-content">
                        <div class="center-align mb-2">
                            <i class="fa-solid fa-key fa-3x icon-blue" style="margin-bottom: 15px;"></i>
                            <h4 class="login-title" style="font-size: 1.5rem;">NOVA SENHA</h4>
                            <p class="login-subtitle">Crie uma senha forte e segura.</p>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            {{-- Campos ocultos obrigatórios do Laravel --}}
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">

                            <div class="input-field">
                                <i class="fa-solid fa-lock prefix icon-blue"></i>
                                <input type="password" name="password" id="password" required class="validate">
                                <label for="password">Nova Senha</label>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-shield-check prefix icon-blue"></i>
                                <input type="password" name="password_confirmation" id="password_confirmation" required class="validate">
                                <label for="password_confirmation">Confirmar Nova Senha</label>
                            </div>

                            <div class="center-align mt-2">
                                <button type="submit" class="btn btn-login shadow-btn full-width">
                                    REDEFINIR SENHA <i class="fa-solid fa-rotate ml-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
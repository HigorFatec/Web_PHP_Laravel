@extends('layout')
@section('title', 'Completar Cadastro - CargoPolo')

@section('conteudo')
<div class="login-wrapper">
    <div class="container">
        <div class="row">
            <div class="col s12 m10 offset-m1 l6 offset-l3">

                {{-- Mensagem de Sucesso (Logout ou retorno) --}}
                @if ($message = Session::get('success'))
                    <div class="card-panel green lighten-4 green-text text-darken-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-check"></i> {{ $message }}
                    </div>
                @endif

                <div class="card card-login">
                    <div class="card-content">
                        <div class="center-align mb-2">
                            <i class="fa-solid fa-address-card fa-3x icon-blue" style="margin-bottom: 15px;"></i>
                            <h4 class="login-title">QUASE LÁ!</h4>
                            <p class="login-subtitle">Complete suas informações para liberar o acesso.</p>
                        </div>

                        <form method="POST" action="/completar-cadastro">
                            @csrf

                            <div class="row">
                                <div class="input-field col s12">
                                    <i class="fa-solid fa-user prefix icon-blue"></i>
                                    <input type="text" name="name" id="name" value="{{ session('ms_user.name') }}" required>
                                    <label for="name">Nome de Usuário</label>
                                </div>

                                <div class="input-field col s12">
                                    <i class="fa-solid fa-id-card prefix icon-blue"></i>
                                    <input type="text" name="cpf" id="cpf" maxlength="11" required 
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);">
                                    <label for="cpf">CPF (Somente números)</label>
                                </div>

                                <div class="input-field col s12">
                                    <i class="fa-solid fa-location-dot prefix icon-blue"></i>
                                    <select name="filial" id="filial" required>
                                        <option value="" disabled selected>Selecione sua Filial</option>
                                        @foreach ($filiais as $filial)
                                            <option value="{{$filial}}">{{$filial}}</option>
                                        @endforeach
                                    </select>
                                    <label>Sua Unidade / Filial</label>
                                </div>

                                <div class="input-field col s12">
                                    <i class="fa-solid fa-user-tie prefix icon-blue"></i>
                                    <input type="email" name="email_gestor" id="email_gestor" required>
                                    <label for="email_gestor">E-mail do Gestor ou Aprovador</label>
                                    <span class="helper-text">E-mail corporativo do seu superior direto.</span>
                                </div>
                            </div>

                            <div class="center-align mt-2">
                                <button type="submit" class="btn btn-login shadow-btn full-width">
                                    FINALIZAR CADASTRO <i class="fa-solid fa-check-double ml-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
    });
</script>
@endsection
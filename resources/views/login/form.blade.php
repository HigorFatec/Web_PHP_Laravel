@extends('layout')
@section('title', 'Grupo Cargo Polo - Login')
@section('conteudo')

@auth
<script>window.location = "/";</script>
@else

{{-- Container para centralização vertical e horizontal --}}
<div class="valign-wrapper" style="min-height: 80vh; width: 100%;">
    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3">

                {{-- Mensagens de Feedback --}}
                @if ($message = Session::get('success'))
                    <div class="card-panel green lighten-4 green-text text-darken-4 center-align" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-check"></i> <b>Sucesso:</b> {{ $message }}
                    </div>
                @endif

                @if ($message = Session::get('erro'))
                    <div class="card-panel red lighten-4 red-text text-darken-4 center-align" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-xmark"></i> <b>Erro:</b> {{ $message }}
                    </div>
                @endif

                <div class="card card-login" style="border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); padding: 20px;">
                    <div class="card-content">
                        
                        {{-- Cabeçalho Centralizado --}}
                        <div class="center-align" style="margin-bottom: 30px;">
                            <h4 style="font-weight: 800; color: #003366; letter-spacing: 1px; margin-bottom: 5px;">PORTAL CORPORATIVO</h4>
                            <p class="grey-text text-darken-1">Acesso exclusivo para colaboradores</p>
                        </div>

                        {{-- Aviso de Domínio com Alto Contraste --}}
                        <div class="card-panel center-align" style="background-color: #e3f2fd; border: 1px solid #2196f3; border-radius: 10px; margin-bottom: 40px; padding: 15px;">
                            <i class="fa-solid fa-circle-info blue-text" style="font-size: 1.8rem; display: block; margin-bottom: 10px;"></i>
                            <span style="color: #0d47a1; font-size: 1.1rem; font-weight: 500; line-height: 1.4;">
                                Para acessar, utilize obrigatoriamente sua conta do <br> 
                                <b style="font-weight: 700;">Grupo Cargo Polo</b> (@grupocargopolo.com.br)
                            </span>
                        </div>

                        {{-- Botão Microsoft Centralizado --}}
                        <div class="center-align">
                            <a href="/auth/microsoft" class="btn-large waves-effect waves-light" 
                               style="width: 100%; max-width: 400px; background-color: #2f2f2f; text-transform: none; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; height: 60px; font-weight: 600; font-size: 1.2rem;">
                                <img src="https://img.icons8.com/color/32/microsoft.png" alt="Microsoft Logo" style="margin-right: 15px;" />
                                Entrar com Microsoft
                            </a>
                        </div>

                        <div class="divider" style="margin: 40px 0 20px 0;"></div>

                        {{-- Link de Suporte Centralizado --}}
                        <div class="center-align">
                            <p class="grey-text text-darken-1" style="font-size: 0.95rem;">
                                Está com problemas para acessar? <br>
                                <a href="https://suporte.grupocargopolo.com.br:13004/WOListView.do" class="blue-text text-darken-4" style="font-weight: 700; text-decoration: none; display: inline-block; margin-top: 10px;">
                                    <i class="fa-solid fa-headset"></i> Abrir um Chamado na TI
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Rodapé --}}
                <div class="center-align grey-text" style="font-size: 0.85rem; margin-top: 25px;">
                    &copy; {{ date('Y') }} Grupo Cargo Polo - Departamento de TI
                </div>

            </div>
        </div>
    </div>
</div>

@endauth

@endsection
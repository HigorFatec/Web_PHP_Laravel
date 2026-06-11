@extends('layout')

@section('title', 'Indicadores BI - CargoPolo')

@section('conteudo')

@if (@auth()->user()->id != null)


    <div class="login-wrapper">
        <div class="container main-container">
            <div class="row">
                <div class="col s12">
                    <div class="card card-login" style="margin-top: 20px; border-radius: 15px;">
                        <div class="card-content" style="padding: 15px;">
                            
                            {{-- Título --}}
                            <div class="center-align mb-2">
                                <h4 class="login-title">INDICADORES BI</h4>
                                <p class="login-subtitle">Análise de Solicitações</p>
                                <span style="font-size: 10px; color: #ccc;">Cache: {{ $veioDoCache }}</span>

                            </div>
                                    
                            {{-- Navegação Responsiva --}}
                            <div class="center-align">
                                <div class="nav-segment-container">
                                    {{-- <a href="{{ route('manutencao.bi') }}" class="nav-segment-btn {{ Request::routeIs('reserva.reservas') ? 'active' : '' }}">
                                        <i class="fa-solid fa-clock-check"></i> <span>Solicitações Aprovadas</span>
                                    </a> --}}

                                    <a href="{{ route('manutencao.bi') }}" class="nav-segment-btn {{ Request::routeIs('reserva.bi') ? 'active' : '' }}">
                                        <i class="fa-solid fa-chart-line"></i> <span>BI</span>
                                    </a>
                                </div>
                            </div>

                            @if(auth()->user()->temSetor(['admin','suprimentos','frotas']))
                            {{-- Container do Power BI --}}
                            <div id="embedContainer" class="z-depth-1"></div>
                            @else

                                <span class="card-title center">Você não tem autorização para ver o <b>Power BI.</b></span>

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Ajuste de Container */
        .main-container { width: 98% !important; }
        
        #embedContainer {
            width: 100%;
            /* No Desktop, 600px a 700px costuma ser o ideal para 16:9 */
            height: 665px; 
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            background: #fff;
        }

        @media (max-width: 600px) {
            #embedContainer {
                height: 450px; /* Altura menor para telas pequenas */
            }
        }
        /* Menu de Segmentos Adaptável */
        .nav-segment-container {
            display: inline-flex;
            background: #f1f3f4;
            padding: 4px;
            border-radius: 50px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
            width: auto;
            max-width: 100%;
        }

        .nav-segment-btn {
            padding: 8px 18px;
            border-radius: 50px;
            text-decoration: none;
            color: #5f6368;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            gap: 5px;
        }

        .nav-segment-btn.active {
            background: #ffffff;
            color: #1a73e8;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .login-title { color: #1a237e; font-weight: bold; font-size: 1.6rem; margin: 10px 0 5px 0; }
        .login-subtitle { color: #757575; font-size: 0.85rem; margin-bottom: 15px; }

        /* Estilos específicos para Mobile */
        @media (max-width: 600px) {
            .login-title { font-size: 1.2rem; }
            .nav-segment-container { border-radius: 10px; width: 100%; }
            .nav-segment-btn { flex: 1; padding: 10px 5px; font-size: 11px; flex-direction: column; border-radius: 8px; }
            .nav-segment-btn i { font-size: 16px; margin: 0; }
            #embedContainer { height: 28vh; } /* Diminui um pouco a altura no mobile */
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/powerbi-client@2.19.1/dist/powerbi.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var models = window['powerbi-client'].models;
            var isMobile = window.innerWidth < 600;

            var config = {
                type: 'report',
                tokenType: models.TokenType.Embed,
                accessToken: '{{ $embedToken }}',
                embedUrl: '{{ $embedUrl }}',
                id: '{{ $reportId }}',
                settings: {
                    panes: {
                        filters: { visible: false },
                        pageNavigation: { visible: false }
                    },
                    background: models.BackgroundType.Default,
                    layoutType: isMobile ? models.LayoutType.MobilePortrait : models.LayoutType.Master,
                    
                    // ALTERAÇÃO AQUI: Força o preenchimento lateral
                    displayOption: models.DisplayOption.FitToWidth 
                }
            };

            var embedContainer = document.getElementById('embedContainer');
            var report = powerbi.embed(embedContainer, config);

            report.on("loaded", function () {
                var iframe = embedContainer.querySelector('iframe');
                if (iframe) iframe.style.border = "none";
            });
        });
    </script>

@else

    <script>
    window.location.href = '/login';
    </script>
@endif

@endsection
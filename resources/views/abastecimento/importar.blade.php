@extends('layout')
@section('title', 'Implantação de Saldo')
@section('conteudo')

@if (@auth()->user()->id != null)
<div class="login-wrapper">
    <div class="container" style="width: 95%;">
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                
                {{-- Alertas de Sucesso --}}
                @if (session('success'))
                    <div class="card green darken-1 shadow-btn" style="border-radius: 8px; margin-bottom: 20px;">
                        <div class="card-content white-text p-1" style="padding: 15px !important;">
                            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                        </div>
                    </div>
                @endif


                @if (session('download_url'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            // Cria um iframe invisível para baixar o arquivo automaticamente sem sair da página
                            const downloadUrl = "{{ session('download_url') }}";
                            const iframe = document.createElement('iframe');
                            iframe.style.display = 'none';
                            iframe.src = downloadUrl;
                            document.body.appendChild(iframe);
                        });
                    </script>
                @endif

                {{-- Alertas de Erro --}}
                @if ($errors->any())
                    <div class="card red darken-1 shadow-btn" style="border-radius: 8px; margin-bottom: 20px;">
                        <div class="card-content white-text p-1" style="padding: 15px !important;">
                            <div style="display: flex; align-items: center; margin-bottom: 8px; font-weight: 700;">
                                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> Atenção aos erros encontrados:
                            </div>
                            <ul style="margin: 0; padding-left: 20px; max-height: 150px; overflow-y: auto;">
                                @foreach ($errors->all() as $erro)
                                    <li style="font-size: 0.85rem; margin-bottom: 4px;">{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- CARD DE IMPORTAÇÃO --}}
                <div class="card shadow-btn" style="border-radius: 15px; margin-bottom: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="card-content white-text" style="padding: 30px !important;">
                        
                        <div style="margin-bottom: 25px;">
                            <span class="card-title fw-bold d-flex align-items-center" style="font-weight: 700; font-size: 1.4rem; color: #f8fafc; margin-bottom: 5px;">
                                <i class="fa-solid fa-file-csv" style="margin-right: 10px; color: #38bdf8;"></i> Importar Abastecimentos
                            </span>
                            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 15px;">Selecione um ficheiro CSV estruturado para realizar a integração em massa.</p>
                            
                            <a href="{{ route('abastecimento.exemploCsv') }}" class="btn-flat d-inline-flex align-items-center" style="color: #38bdf8; text-transform: none; font-weight: 600; padding: 0; font-size: 0.85rem; gap: 6px; background: transparent; box-shadow: none;">
                                <i class="fa-solid fa-download" style="font-size: 0.95rem;"></i> Descarregar modelo de exemplo (.CSV)
                            </a>
                        </div>

                        <form action="{{ route('abastecimento.importar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="file-field input-field" style="margin-top: 30px; margin-bottom: 30px;">
                                <div class="btn blue-grey darken-3 shadow-btn" style="border-radius: 8px; text-transform: none; font-weight: 600;">
                                    <span><i class="fa-solid fa-folder-open"></i> Procurar</span>
                                    <input type="file" id="arquivo" name="arquivo" accept=".csv" required>
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate white-text" type="text" placeholder="Nenhum ficheiro selecionado" style="border-bottom: 1px solid #475569 !important; box-shadow: none !important;">
                                </div>
                            </div>

                            <div style="background: rgba(255, 255, 255, 0.04); padding: 12px 15px; border-radius: 8px; border-left: 4px solid #38bdf8; margin-bottom: 30px;">
                                <span style="font-size: 0.8rem; color: #94a3b8; display: block; margin-bottom: 3px; font-weight: 600;">Cabeçalhos obrigatórios do CSV:</span>
                                <code style="font-family: monospace; color: #cbd5e1; font-size: 0.78rem; word-break: break-all;">CODCON; NUMDOC; Placa Cavalo Trator; VALOR; CODMOT...</code>
                            </div>

                            <div class="display-flex justify-content-end" style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn shadow-btn" style="border-radius: 8px; background: #0284c7; text-transform: none; font-weight: 600; padding: 0 25px; height: 42px; display: inline-flex; align-items: center; gap: 8px;">
                                    Processar Ficheiro <i class="fa-solid fa-cloud-arrow-up"></i>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endif

@endsection
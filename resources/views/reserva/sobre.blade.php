@extends('layout')

@section('title', 'Sobre o Sistema')
@section('conteudo')

<div class="container enterprise-container">

    <div class="enterprise-card">

        <!-- HEADER -->
        <div class="enterprise-header">
            <h1>Sobre o Sistema</h1>
            <p>Sistema corporativo de apoio a processos operacionais</p>
        </div>

        <!-- BLOCO PRINCIPAL -->
        <div class="enterprise-section">
            <h2>Visão Geral</h2>
            <p>
                Esta aplicação foi desenvolvida com foco em
                <strong>estabilidade</strong>, <strong>segurança</strong> e
                <strong>padronização de processos</strong>, atendendo demandas
                reais de ambiente corporativo.
            </p>

            <p>
                O sistema foi projetado para facilitar operações do dia a dia,
                centralizar informações e reduzir falhas operacionais,
                priorizando clareza e eficiência.
            </p>
        </div>

        <!-- STACK -->
        <div class="enterprise-section">
            <h2>Tecnologias Utilizadas</h2>

            <div class="stack-grid">
                <span class="stack-item">Laravel (Framework PHP)</span>
                <span class="stack-item">PHP 8+</span>
                <span class="stack-item">JavaScript (ES6+)</span>
                <span class="stack-item">HTML5</span>
                <span class="stack-item">CSS3</span>
                <span class="stack-item">MySQL / SQL Server</span>
            </div>
        </div>

        <!-- INFO SISTEMA -->
        <div class="enterprise-section">
            <h2>Informações do Sistema</h2>

            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Versão</span>
                    <span class="value">v1.0.0</span>
                </div>

                <div class="info-item">
                    <span class="label">Ambiente</span>
                    <span class="value">Produção</span>
                </div>

                <div class="info-item">
                    <span class="label">Última Atualização</span>
                    <span class="value">{{ date('d/m/Y') }}</span>
                </div>

                <div class="info-item">
                    <span class="label">Arquitetura</span>
                    <span class="value">MVC</span>
                </div>
            </div>
        </div>

        <!-- DIVISOR -->
        <div class="enterprise-divider"></div>

        <!-- FOOTER -->
        <div class="enterprise-footer">
            <div class="author-block">
                <span>Responsável Técnico</span>
                <strong>Higor Machado</strong>
            </div>

            <div class="footer-actions">
                <a href="https://wa.me/5513978090383?text=Olá%20Higor,%20gostaria%20de%20conversar%20sobre%20seu%20sistema."
                  target="_blank" class="btn-enterprise primary">
                    Contato Técnico
                </a>

                <a href="https://www.linkedin.com/in/higor-dos-santos-machado-9a269a139/"
                  target="_blank" class="btn-enterprise secondary">
                    LinkedIn
                </a>

                <a href="https://www.higormachado.com.br"
                  target="_blank" class="btn-enterprise secondary">
                    Portfólio
                </a>
            </div>

        </div>

    </div>

</div>

<style>
    body {
        background: #f4f6f9 !important;
        font-family: 'Segoe UI', Roboto, Arial, sans-serif;
    }

    .enterprise-container {
        margin-top: 120px;
        max-width: 1100px;
    }

    .enterprise-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 60px 55px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.07);
    }

    .enterprise-header h1 {
        font-size: 30px;
        font-weight: 600;
        color: #1f2933;
        margin-bottom: 6px;
    }

    .enterprise-header p {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 45px;
    }

    .enterprise-section {
        margin-bottom: 45px;
    }

    .enterprise-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2933;
        margin-bottom: 14px;
    }

    .enterprise-section p {
        font-size: 16px;
        line-height: 1.85;
        color: #475569;
        margin-bottom: 14px;
    }

    .stack-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .stack-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-top: 15px;
    }

    .info-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .value {
        font-size: 15px;
        font-weight: 600;
        color: #1f2933;
    }

    .enterprise-divider {
        height: 1px;
        background: linear-gradient(
            90deg,
            transparent,
            #e2e8f0,
            transparent
        );
        margin: 55px 0;
    }

    .enterprise-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 25px;
    }

    .author-block span {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .author-block strong {
        font-size: 16px;
        color: #1f2933;
    }

    .footer-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-enterprise {
        padding: 12px 26px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s ease;
    }

    .btn-enterprise.primary {
        background: #0f172a;
        color: #ffffff;
    }

    .btn-enterprise.primary:hover {
        background: #020617;
        transform: translateY(-2px);
    }

    .btn-enterprise.secondary {
        border: 2px solid #cbd5e1;
        color: #334155;
    }

    .btn-enterprise.secondary:hover {
        background: #334155;
        color: #ffffff;
        border-color: #334155;
    }
</style>

@endsection

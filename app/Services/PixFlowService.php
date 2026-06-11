<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PixFlowService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.pixflow.base_url');
        $this->apiKey = config('services.pixflow.api_key');
    }

    /**
     * Envia uma nova solicitação de pagamento para a Pix Flow F&R
     */
    public function criarSolicitacao(array $payload): array
    {
        $url = "{$this->baseUrl}/api/public/solicitacoes";
        
        // Dispara a requisição contra a API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key'    => $this->apiKey,
        ])->post($url, $payload);

        $status = $response->status();
        $dados = $response->json();

        // 1. Sucesso no Envio (200 = Idempotente / 201 = Criada com Sucesso)
        if (in_array($status, [200, 201])) {
            return [
                'sucesso'     => true,
                'status'      => $status,
                'id'          => $dados['id'] ?? null,
                'pix_status'  => $dados['status'] ?? 'recebida',
                'idempotent'  => $dados['idempotent'] ?? false,
                'mensagem'    => 'Solicitação registrada na Pix Flow.'
            ];
        }

        // 2. Erros de Negócio Tratados (422)
        if ($status === 422) {
            // Caso A: Bloqueio do DICT (Divergência de Titularidade)
            if (isset($dados['error']) && $dados['error'] === 'PIX_VALIDATION_BLOCKED') {
                return [
                    'sucesso' => false,
                    'status'  => $status,
                    'erro_tipo' => 'VALIDATION_BLOCKED',
                    'mensagem' => "Bloqueado pelo DICT: Divergência de {$dados['motivo']}.",
                    'detalhes' => [
                        'titular_real' => $dados['titular_nome'] ?? 'Desconhecido',
                        'banco' => $dados['banco'] ?? 'Não informado'
                    ]
                ];
            }

            // Caso B: Falta de Saldo Bancário Interno na Plataforma
            if (isset($dados['error']) && $dados['error'] === 'INSUFFICIENT_BALANCE') {
                return [
                    'sucesso' => false,
                    'status'  => $status,
                    'erro_tipo' => 'INSUFFICIENT_BALANCE',
                    'mensagem' => 'Saldo interno insuficiente na conta Pix Flow F&R.'
                ];
            }
        }

        // 3. Caso de Duplicidade / Anti-fraude (409)
        if ($status === 409) {
            return [
                'sucesso' => false,
                'status'  => $status,
                'erro_tipo' => 'DUPLICATE',
                'mensagem' => 'Pagamento rejeitado: Identificada duplicidade ou requisição concorrente.'
            ];
        }

        // 4. Qualquer outro erro crítico de infraestrutura (400, 401, 403, 429, 500)
        Log::critical("Erro Crítico na API Pix Flow F&R [Status {$status}]", [
            'payload' => $payload,
            'resposta' => $response->body()
        ]);

        return [
            'sucesso' => false,
            'status'  => $status,
            'erro_tipo' => 'CRITICAL_ERROR',
            'mensagem' => "Erro na API corporativa (Código HTTP: {$status})."
        ];
    }
}
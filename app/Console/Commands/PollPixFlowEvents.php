<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PollPixFlowEvents extends Command
{
    // O comando que você usará no terminal ou cron
    protected $signature = 'pixflow:poll';
    protected $description = 'Consulta a API Pix Flow para atualizar os status das solicitações';

    public function handle()
    {
        $baseUrl = config('services.pixflow.base_url');
        $apiKey = config('services.services.api_key') ?? config('services.pixflow.api_key');

        $this->info('Iniciando busca de eventos na Pix Flow...');

        try {
            // 1. Consome os eventos pendentes (Traz até 50 por padrão)
            $response = Http::withHeaders([
                'x-api-key' => $apiKey
            ])->get("{$baseUrl}/api/public/events", [
                'status' => 'pending',
                'limit' => 50
            ]);

            if ($response->failed()) {
                Log::error("Falha ao buscar eventos do Pix Flow: HTTP " . $response->status());
                return Command::FAILURE;
            }

            $dados = $response->json();
            $events = $dados['events'] ?? [];

            if (empty($events)) {
                $this->info('Nenhum evento novo pendente.');
                return Command::SUCCESS;
            }

            foreach ($events as $ev) {
                $eventId = $ev['id'];
                $eventType = $ev['event_type']; // ex: 'pagamento.atualizado'
                $payload = $ev['payload'] ?? [];
                
                // O external_id que enviamos na aprovação (ex: "pedido-19429")
                $externalId = $payload['external_id'] ?? null; 

                try {
                    // Se o evento contiver o ID do nosso sistema, atualizamos o banco
                    if ($externalId) {
                        // Extrai apenas os números para achar o ID real no seu banco (pedido-19429 -> 19429)
                        $idLocal = (int) preg_replace('/[^0-9]/', '', $externalId);
                        
                        // Captura o novo status vindo da API da FR (pago, falha, processando, etc)
                        $novoStatusAPI = $payload['status'] ?? null;

                        if ($novoStatusAPI) {
                            // Atualiza a tabela do Grupo Cargo Polo
                            DB::table('financeiros')
                                ->where('id', $idLocal)
                                ->update([
                                    'status' => $novoStatusAPI, // vai salvar 'pago', 'falha' ou 'processando'
                                    'updated_at' => now()
                                ]);
                                
                            $this->info("Pedido #{$idLocal} atualizado para o status: {$novoStatusAPI}");
                        }
                    }

                    // 2. CONFIRMAÇÃO DE RECEBIMENTO (ACK) - Importante para o evento não vir repetido
                    Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'x-api-key' => $apiKey
                    ])->post("{$baseUrl}/api/public/events", [
                        'id' => $eventId,
                        'status' => 'sent'
                    ]);

                } catch (\Exception $e) {
                    // Se o seu banco falhar, avisa a API marcando como failed (ela aplicará o backoff)
                    Log::error("Erro ao processar evento individual {$eventId}: " . $e->getMessage());
                    
                    Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'x-api-key' => $apiKey
                    ])->post("{$baseUrl}/api/public/events", [
                        'id' => $eventId,
                        'status' => 'failed',
                        'error' => substr($e->getMessage(), 0, 490)
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::critical("Erro geral no comando pixflow:poll -> " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info('Polling finalizado com sucesso!');
        return Command::SUCCESS;
    }
}
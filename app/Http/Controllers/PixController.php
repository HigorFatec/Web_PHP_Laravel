<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PixController extends Controller
{
    public function validatePixOwner(Request $request)
    {
        $request->validate([
            'pix' => 'required|string'
        ]);

        $key = $request->pix;
        // A função abaixo limpa a chave (tira pontos, adiciona +55, etc)
        $type = $this->identifyPixType($key);

        // VOLTAMOS PARA A URL DE CONSULTA DE CHAVE
        // Nota: Esta URL exige que sua conta Mercado Pago tenha permissões de Payouts/Transferências
        $response = Http::withToken(config('services.mercadopago.token'))
                    ->get("https://api.mercadopago.com/v1/pix/keys/{$type}/{$key}");

        if ($response->failed()) {
            return response()->json([
                'error' => 'Chave não encontrada ou sem permissão de acesso',
                'details' => $response->json()
            ], $response->status());
        }

        $data = $response->json();

        return response()->json([
            'owner' => [
                // Ajustamos os índices baseados no retorno padrão da API de Payouts
                'name' => $data['owner']['display_name'] ?? 
                          ($data['owner']['first_name'] . ' ' . ($data['owner']['last_name'] ?? '')) ?? 
                          'Nome não disponível',
                'tax_id' => $data['owner']['tax_id'] ?? ''
            ]
        ]);
    }

    private function identifyPixType(&$key) {
        $key = trim($key);

        if (filter_var($key, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        $onlyNumbers = preg_replace('/\D/', '', $key);
        
        if (strlen($onlyNumbers) === 11) {
            $key = $onlyNumbers;
            return 'cpf';
        }
        
        if (strlen($onlyNumbers) === 14) {
            $key = $onlyNumbers;
            return 'cnpj';
        }

        if (strlen($onlyNumbers) >= 10 && strlen($onlyNumbers) <= 11) {
            $key = '+55' . $onlyNumbers;
            return 'phone';
        }

        return 'evp';
    }
}
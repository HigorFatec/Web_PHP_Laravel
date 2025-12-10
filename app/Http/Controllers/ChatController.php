<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Salva uma nova mensagem no banco de dados.
     */
    public function sendMessage(Request $request)
    {
        // 1. Validação
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // 2. Criação da mensagem
        $message = Message::create([
            'user_id' => Auth::id(), // ID do usuário logado
            'content' => $request->content,
        ]);

        // 3. Retorna a nova mensagem (opcional, mas bom para atualizar a tela de quem enviou)
        return response()->json([
            'success' => true,
            'message' => $message,
            'user_name' => Auth::user()->name ?? 'Usuário Desconhecido' // Nome do usuário
        ], 201);
    }

    /**
     * Busca novas mensagens desde um determinado timestamp.
     */
public function fetchMessages(Request $request)
    {
        // Usa `get` para pegar o valor, que será NULL se não estiver presente.
        // Se o JS enviar 'null', ele será uma string 'null', que é tratada no `if` abaixo.
        $last_timestamp = $request->get('last_timestamp'); 

        $query = Message::with('user'); 

        // Condição para carregar o HISTÓRICO (após F5)
        // Entra aqui se for NULL ou se for a string "null" (que o JS pode enviar)
        if (empty($last_timestamp) || $last_timestamp === 'null') {
            
            // MODO HISTÓRICO: Busca as 50 mensagens mais recentes
            $messages = $query->latest() // Ordena pelas mais recentes (desc)
                              ->take(50)  // Limita a 50 mensagens
                              // Reverte a ordem para que o JS exiba da mais antiga para a mais nova
                              ->orderBy('created_at', 'asc') 
                              ->get();
                              
        } else {
            // MODO POLLING: Busca APENAS mensagens mais novas que o último timestamp
            $messages = $query->where('created_at', '>', $last_timestamp)
                              ->orderBy('created_at', 'asc')
                              ->get();
        }

        // Calcula o timestamp da última mensagem carregada.
        $latest_timestamp = $messages->isNotEmpty() 
            ? $messages->last()->created_at->toDateTimeString() 
            : $last_timestamp;

        return response()->json([
            'messages' => $messages,
            'last_timestamp' => $latest_timestamp,
        ]);
    }
}
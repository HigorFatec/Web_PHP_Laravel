<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSetor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$setoresRequeridos)
    {
        if (auth()->check()) {
            // Pega todos os nomes de setores que o usuário logado tem
            $setoresDoUsuario = auth()->user()->setores->pluck('nome')->toArray();

            // Verifica se algum dos setores do usuário está na lista permitida da rota
            foreach ($setoresRequeridos as $setor) {
                if (in_array($setor, $setoresDoUsuario)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Acesso negado para o seu setor.');
    }
}

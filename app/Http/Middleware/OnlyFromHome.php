<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnlyFromHome
{
    public function handle(Request $request, Closure $next)
    {
        // Todas as rotas da dashboard que devem ser protegidas
        $protectedRoutes = [
            'financeiro.index',
            'empresa.create',
            'fiscal.index',
            'pagamento_pix.index',
            'florestal_pix.index',
            'saldo.index',
            'produtos.create',
            'transf_veiculo.index',
            'descarte.index',
            'sinistro.index',

        ];

        if (in_array($request->route()->getName(), $protectedRoutes)) {
            if (!$request->session()->has('from_home')) {
                return redirect('/');
            }
            $request->session()->forget('from_home');
        }

        return $next($request);
    }
}

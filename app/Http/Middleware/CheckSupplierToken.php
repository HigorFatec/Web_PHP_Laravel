<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSupplierToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token !== env('KM_SUPPLIER_TOKEN')) {
            return response()->json([
                'message' => 'Token inválido'
            ], 401);
        }

        return $next($request);
    }
}

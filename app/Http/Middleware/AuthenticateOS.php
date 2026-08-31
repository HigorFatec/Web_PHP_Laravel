<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class AuthenticateOS extends Middleware
{
    /**
     * Redirecionamento exclusivo para quando a autenticação de OS falhar.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login'); // Sua rota de login de OS
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UpdateActiveVisitors
{
    public function handle(Request $request, Closure $next)
    {
        $sessionId = Session::getId();

        DB::table('active_visitors')->updateOrInsert(
            ['session_id' => $sessionId],
            ['last_active' => now()]
        );

        // Remove visitantes inativos (por exemplo, não ativos por mais de 30 minutos)
        DB::table('active_visitors')
            ->where('last_active', '<', now()->subMinutes(30))
            ->delete();

        return $next($request);
    }
}

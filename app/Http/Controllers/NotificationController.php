<?php

namespace App\Http\Controllers;

use App\Models\NotificationCustom;
use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
public function fetch()
{
    $userId = auth()->id();

    // Se o usuário estiver logado, pegamos o que ele já leu
    $readIds = [];
    if ($userId) {
        $readIds = \DB::table('notification_reads')
            ->where('user_id', $userId)
            ->pluck('notification_id')
            ->toArray();
    }

    $notifications = \App\Models\NotificationCustom::where(function($query) use ($userId) {
            // 1. Sempre busca as globais
            $query->where('is_global', true);
            
            // 2. Se estiver logado, busca também as privadas dele
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })
        // 3. Se estiver logado, remove as que ele já leu
        // Se for visitante ($userId é null), ele vê todas as globais
        ->when($userId, function ($query) use ($readIds) {
            return $query->whereNotIn('id', $readIds);
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    return response()->json([
        'notifications' => $notifications,
        'unread_count' => $notifications->count(),
        'is_logged_in' => !!$userId // Avisa ao JS se o usuário está logado
    ]);
}

public function markAsRead($id)
{
    $userId = auth()->id();
    $notification = \App\Models\NotificationCustom::findOrFail($id);

    // Só gravamos a leitura no banco se houver um usuário logado
    if ($userId) {
        \DB::table('notification_reads')->updateOrInsert(
            ['user_id' => $userId, 'notification_id' => $id],
            ['created_at' => now(), 'updated_at' => now()]
        );
    }

    // Se tiver URL, redireciona (funciona para logados e visitantes)
    if ($notification->url) {
        return redirect($notification->url);
    }

    return redirect()->back();
}

}
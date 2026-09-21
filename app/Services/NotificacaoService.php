<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Notificacao;

class NotificacaoService
{
    public static function enviar(int $adminId, string $tipo, string $titulo, ?string $msg = null, ?string $url = null): void
    {
        Notificacao::create([
            'admin_id' => $adminId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensagem' => $msg,
            'url' => $url,
        ]);
    }

    public static function enviarParaRole(string $role, string $tipo, string $titulo, ?string $msg = null, ?string $url = null): void
    {
        Admin::where('role', $role)->where('ativo', true)->each(
            fn ($a) => self::enviar($a->id, $tipo, $titulo, $msg, $url)
        );
    }

    public static function contagemNaoLidas(int $adminId): int
    {
        return Notificacao::where('admin_id', $adminId)->where('lida', false)->count();
    }
}

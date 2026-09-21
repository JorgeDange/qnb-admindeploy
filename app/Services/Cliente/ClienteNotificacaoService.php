<?php

namespace App\Services\Cliente;

use App\Models\ClienteNotificacao;

class ClienteNotificacaoService
{
    /**
     * Envia (cria) uma notificação in-app para um cliente.
     */
    public static function enviar(
        int $clienteId,
        string $tipo,
        string $titulo,
        string $mensagem,
        ?string $url = null
    ): ClienteNotificacao {
        return ClienteNotificacao::create([
            'cliente_id' => $clienteId,
            'tipo'       => $tipo,
            'titulo'     => $titulo,
            'mensagem'   => $mensagem,
            'url'        => $url,
        ]);
    }
}

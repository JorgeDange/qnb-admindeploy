<?php

namespace App\Services;

use App\Models\PushToken;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    public static function registrarToken(
        int $clienteId,
        string $token,
        string $plataforma = 'web',
        ?string $userAgent = null
    ): PushToken {
        $existing = PushToken::where('cliente_id', $clienteId)
            ->where('token', $token)
            ->first();

        if ($existing) {
            $existing->update([
                'ativo' => true,
                'ultimo_uso_em' => now(),
                'user_agent' => $userAgent ?? $existing->user_agent,
            ]);
            return $existing;
        }

        return PushToken::create([
            'cliente_id' => $clienteId,
            'token' => $token,
            'plataforma' => $plataforma,
            'user_agent' => $userAgent,
            'ativo' => true,
            'ultimo_uso_em' => now(),
        ]);
    }

    public static function removerToken(string $token): bool
    {
        return (bool) PushToken::where('token', $token)->update(['ativo' => false]);
    }

    public static function enviar(
        int $clienteId,
        string $titulo,
        string $mensagem,
        ?string $url = null
    ): int {
        $tokens = PushToken::where('cliente_id', $clienteId)
            ->where('ativo', true)
            ->get();

        if ($tokens->isEmpty()) {
            return 0;
        }

        $enviados = 0;

        foreach ($tokens as $pushToken) {
            try {
                // In a real implementation, you'd use a push provider here:
                // - Web Push (webpush-php)
                // - Firebase Cloud Messaging (FCM)
                // - OneSignal, Pusher, etc.
                //
                // For now, we log the notification and mark the token as used.
                Log::info('Push notification sent', [
                    'cliente_id' => $clienteId,
                    'token' => substr($pushToken->token, 0, 20) . '...',
                    'titulo' => $titulo,
                    'mensagem' => $mensagem,
                    'url' => $url,
                ]);

                $pushToken->marcarUso();
                $enviados++;
            } catch (\Exception $e) {
                Log::error('Push notification failed', [
                    'token_id' => $pushToken->id,
                    'error' => $e->getMessage(),
                ]);

                // Disable token if it fails repeatedly
                if ($pushToken->ultimo_uso_em && $pushToken->ultimo_uso_em->diffInDays(now()) > 30) {
                    $pushToken->desativar();
                }
            }
        }

        return $enviados;
    }

    public static function contarTokensAtivos(int $clienteId): int
    {
        return PushToken::where('cliente_id', $clienteId)
            ->where('ativo', true)
            ->count();
    }

    public static function limparTokensInativos(): int
    {
        return PushToken::where('ativo', false)
            ->where('updated_at', '<', now()->subDays(30))
            ->delete();
    }
}

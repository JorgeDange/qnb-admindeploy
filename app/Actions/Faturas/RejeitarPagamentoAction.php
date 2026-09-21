<?php

namespace App\Actions\Faturas;

use App\Models\Fatura;
use App\Models\ImobiliariaNotificacao;
use App\Services\ActivityLogService;
use App\Services\FaturaService;
use Illuminate\Support\Facades\Mail;

class RejeitarPagamentoAction
{
    public function __construct(
        private FaturaService $faturaService,
    ) {}

    public function execute(Fatura $fatura, int $adminId, string $motivo): Fatura
    {
        $fatura->update([
            'estado' => 'rejeitada',
            'motivo_rejeicao' => $motivo,
            'rejeitada_por' => $adminId,
            'rejeitada_em' => now(),
        ]);

        // Enviar email à imobiliária
        $imobiliaria = $fatura->imobiliaria;
        if ($imobiliaria && $imobiliaria->email) {
            Mail::to($imobiliaria->email)->queue(
                new \App\Mail\ComprovativoRejeitadoMail($fatura)
            );
            $this->faturaService->registrarEmail($fatura, 'comprovativo_rejeitado', $imobiliaria->email);
        }

        ActivityLogService::log('comprovativo_rejeitado', $fatura, [
            'fatura_numero' => $fatura->numero,
            'imobiliaria_id' => $fatura->imobiliaria_id,
            'motivo' => $motivo,
            'rejeitada_por' => $adminId,
        ]);

        // Notificação in-app para a imobiliária
        ImobiliariaNotificacao::criar(
            $fatura->imobiliaria_id,
            'fatura_rejeitada',
            'Comprovativo Rejeitado',
            "Fatura {$fatura->numero} — comprovativo rejeitado. Motivo: {$motivo}",
            rtrim(config('app.frontend_url', ''), '/') . '/painel/faturas/' . $fatura->id
        );

        return $fatura->fresh();
    }
}

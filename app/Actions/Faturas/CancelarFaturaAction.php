<?php

namespace App\Actions\Faturas;

use App\Models\Fatura;
use App\Models\ImobiliariaNotificacao;
use App\Services\ActivityLogService;
use App\Services\FaturaService;
use Illuminate\Support\Facades\Mail;

class CancelarFaturaAction
{
    public function __construct(
        private FaturaService $faturaService,
    ) {}

    public function execute(Fatura $fatura, int $adminId, ?string $motivo = null): Fatura
    {
        $fatura->update([
            'estado' => 'cancelada',
            'motivo_rejeicao' => $motivo ?? 'Fatura cancelada pelo administrador',
            'rejeitada_por' => $adminId,
            'rejeitada_em' => now(),
        ]);

        $imobiliaria = $fatura->imobiliaria;
        if ($imobiliaria && $imobiliaria->email) {
            Mail::to($imobiliaria->email)->queue(
                new \App\Mail\FaturaCanceladaMail($fatura)
            );
            $this->faturaService->registrarEmail($fatura, 'fatura_cancelada', $imobiliaria->email);
        }

        ActivityLogService::log('fatura_cancelada', $fatura, [
            'fatura_numero' => $fatura->numero,
            'imobiliaria_id' => $fatura->imobiliaria_id,
            'motivo' => $motivo,
            'cancelada_por' => $adminId,
        ]);

        // Notificação in-app para a imobiliária
        ImobiliariaNotificacao::criar(
            $fatura->imobiliaria_id,
            'fatura_cancelada',
            'Fatura Cancelada',
            "Fatura {$fatura->numero} — cancelada pelo administrador.",
            rtrim(config('app.frontend_url', ''), '/') . '/painel/faturas/' . $fatura->id
        );

        return $fatura->fresh();
    }
}

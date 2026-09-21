<?php

namespace App\Actions\Faturas;

use App\Models\Fatura;
use App\Services\ActivityLogService;
use App\Services\FaturaService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Mail;

class EmitirFaturaAction
{
    public function __construct(
        private InvoiceService $invoiceService,
        private FaturaService $faturaService,
    ) {}

    public function execute(Fatura $fatura): Fatura
    {
        if (!$fatura->pdf_path) {
            $this->invoiceService->gerarPdf($fatura);
        }

        $fatura->update([
            'estado' => 'pendente',
            'emitida_em' => $fatura->emitida_em ?? now(),
        ]);

        $imobiliaria = $fatura->imobiliaria;
        if ($imobiliaria && $imobiliaria->email) {
            Mail::to($imobiliaria->email)->queue(
                new \App\Mail\FaturaEmitidaMail($fatura)
            );
            $this->faturaService->registrarEmail($fatura, 'fatura_emitida', $imobiliaria->email);
        }

        ActivityLogService::log('fatura_emitida', $fatura, [
            'fatura_numero' => $fatura->numero,
            'imobiliaria_id' => $fatura->imobiliaria_id,
            'total' => $fatura->total,
        ]);

        return $fatura->fresh();
    }
}

<?php

namespace App\Actions\Faturas;

use App\Models\Fatura;
use App\Models\ImobiliariaNotificacao;
use App\Services\ActivityLogService;
use App\Services\FaturaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SubmeterComprovativoAction
{
    public function __construct(
        private FaturaService $faturaService,
    ) {}

    public function execute(Fatura $fatura, UploadedFile $comprovativo, int $imobiliariaId): Fatura
    {
        $caminho = $comprovativo->store('pagamentos/comprovativos', 'public');

        $fatura->update([
            'comprovativo_path' => $caminho,
            'comprovativo_enviado_em' => now(),
            'comprovativo_enviado_por' => $imobiliariaId,
            'estado' => 'aguarda_aprovacao',
        ]);

        // Enviar email ao admin
        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(
                new \App\Mail\ComprovativoSubmetidoMail($fatura)
            );
            $this->faturaService->registrarEmail($fatura, 'comprovativo_submetido', $adminEmail);
        }

        ActivityLogService::log('comprovativo_submetido', $fatura, [
            'fatura_numero' => $fatura->numero,
            'imobiliaria_id' => $fatura->imobiliaria_id,
            'comprovativo_path' => $caminho,
        ]);

        // Notificação in-app para o admin
        ImobiliariaNotificacao::criar(
            $fatura->imobiliaria_id,
            'fatura_comprovativo',
            'Comprovativo Submetido',
            "Fatura {$fatura->numero} — comprovativo de pagamento submetido, aguarda aprovação.",
            route('admin.faturas.show', $fatura)
        );

        return $fatura->fresh();
    }
}

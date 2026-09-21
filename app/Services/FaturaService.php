<?php

namespace App\Services;

use App\Models\Fatura;
use App\Models\FaturaEmailLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class FaturaService
{
    public function __construct(
        private EmpresaConfigService $configService,
    ) {}

    /**
     * Gerar PDF do recibo de uma fatura paga.
     */
    public function gerarPdfRecibo(Fatura $fatura): string
    {
        $fatura->load(['pagamento', 'imobiliaria', 'linhas']);

        $snapshot = $this->configService->snapshot();
        $configFatura = $this->configService->configFatura();

        $pdf = Pdf::loadView('pdf.recibo', [
            'fatura' => $fatura,
            'empresa' => $snapshot,
            'configFatura' => $configFatura,
        ]);

        $pdf->setPaper('a4');
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        $nomeFicheiro = 'faturas/recibos/' . str_replace('/', '-', $fatura->recibo_numero) . '.pdf';
        $caminhoCompleto = storage_path('app/public/' . $nomeFicheiro);

        if (!is_dir(storage_path('app/public/faturas/recibos'))) {
            mkdir(storage_path('app/public/faturas/recibos'), 0755, true);
        }

        file_put_contents($caminhoCompleto, $pdf->output());

        $fatura->update([
            'recibo_pdf_path' => $nomeFicheiro,
            'recibo_emitido_em' => now(),
        ]);

        return $nomeFicheiro;
    }

    /**
     * Download do recibo.
     */
    public function downloadRecibo(Fatura $fatura)
    {
        if (!$fatura->recibo_pdf_path || !Storage::disk('public')->exists($fatura->recibo_pdf_path)) {
            $this->gerarPdfRecibo($fatura);
        }

        $nomeSeguro = str_replace(['/', '\\'], '-', $fatura->recibo_numero);

        return Storage::disk('public')->download(
            $fatura->recibo_pdf_path,
            'Recibo-' . $nomeSeguro . '.pdf'
        );
    }

    /**
     * Registar envio de email na fatura_emails_log.
     */
    public function registrarEmail(Fatura $fatura, string $tipoEmail, string $destinatario, array $metadata = []): FaturaEmailLog
    {
        $log = FaturaEmailLog::create([
            'fatura_id' => $fatura->id,
            'tipo_email' => $tipoEmail,
            'destinatario' => $destinatario,
            'enviado_em' => now(),
            'metadata' => $metadata,
        ]);

        // Atualizar timestamp na fatura conforme o tipo
        $campoTimestamp = match ($tipoEmail) {
            'fatura_emitida' => 'email_emitida_em',
            'comprovativo_submetido' => 'email_comprovativo_em',
            'pagamento_confirmado', 'comprovativo_rejeitado' => 'email_paga_em',
            default => null,
        };

        if ($campoTimestamp) {
            $fatura->update([$campoTimestamp => now()]);
        }

        return $log;
    }

    /**
     * Buscar faturas com filtro de estado.
     */
    public function buscarComFiltro(?string $estado = null, ?int $imobiliariaId = null)
    {
        $query = Fatura::with(['imobiliaria', 'linhas']);

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($imobiliariaId) {
            $query->where('imobiliaria_id', $imobiliariaId);
        }

        return $query->orderByDesc('emitida_em');
    }
}

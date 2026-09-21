<?php

namespace App\Services;

use App\Models\Fatura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function __construct(
        private EmpresaConfigService $configService,
    ) {}

    public function gerarPdf(Fatura $fatura): string
    {
        $fatura->load(['pagamento', 'imobiliaria', 'pagamento.subscricao.plano', 'linhas']);

        // Snapshot da empresa para a fatura (dados imutáveis)
        $snapshot = $this->configService->snapshot();
        $configFatura = $this->configService->configFatura();

        $pdf = Pdf::loadView('pdf.fatura', [
            'fatura' => $fatura,
            'empresa' => $snapshot,
            'configFatura' => $configFatura,
        ]);

        $pdf->setPaper('a4');
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        // Usar DejaVu Sans para suporte a caracteres especiais (ã, ç, á, etc.)
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        $nomeFicheiro = 'faturas/' . str_replace('/', '-', $fatura->numero) . '.pdf';
        $caminhoCompleto = storage_path('app/public/' . $nomeFicheiro);

        if (!is_dir(storage_path('app/public/faturas'))) {
            mkdir(storage_path('app/public/faturas'), 0755, true);
        }

        file_put_contents($caminhoCompleto, $pdf->output());

        $fatura->update(['pdf_path' => $nomeFicheiro]);

        return $nomeFicheiro;
    }

    public function download(Fatura $fatura)
    {
        if (!$fatura->pdf_path || !Storage::disk('public')->exists($fatura->pdf_path)) {
            $this->gerarPdf($fatura);
        }

        $nomeSeguro = str_replace(['/', '\\'], '-', $fatura->numero);

        return Storage::disk('public')->download(
            $fatura->pdf_path,
            'Fatura-' . $nomeSeguro . '.pdf'
        );
    }

    public function gerarInline(Fatura $fatura)
    {
        if (!$fatura->pdf_path || !Storage::disk('public')->exists($fatura->pdf_path)) {
            $this->gerarPdf($fatura);
        }

        $nomeSeguro = str_replace(['/', '\\'], '-', $fatura->numero);

        return Storage::disk('public')->download(
            $fatura->pdf_path,
            'Fatura-' . $nomeSeguro . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}

<?php

namespace App\Actions\Faturas;

use App\Models\Fatura;
use App\Models\ImobiliariaNotificacao;
use App\Models\ImobiliariaPlano;
use App\Services\ActivityLogService;
use App\Services\FaturaService;
use Illuminate\Support\Facades\Mail;

class AprovarPagamentoAction
{
    public function __construct(
        private FaturaService $faturaService,
    ) {}

    public function execute(Fatura $fatura, int $adminId): Fatura
    {
        $reciboNumero = Fatura::proximoNumeroRecibo();

        $fatura->update([
            'estado' => 'paga',
            'aprovada_por' => $adminId,
            'aprovada_em' => now(),
            'recibo_numero' => $reciboNumero,
        ]);

        // Gerar PDF do recibo
        $this->faturaService->gerarPdfRecibo($fatura);

        // Ativar plano da imobiliária
        $this->ativarPlano($fatura);

        // Enviar email à imobiliária
        $imobiliaria = $fatura->imobiliaria;
        if ($imobiliaria && $imobiliaria->email) {
            Mail::to($imobiliaria->email)->queue(
                new \App\Mail\PagamentoConfirmadoMail($fatura)
            );
            $this->faturaService->registrarEmail($fatura, 'pagamento_confirmado', $imobiliaria->email);
        }

        ActivityLogService::log('pagamento_confirmado', $fatura, [
            'fatura_numero' => $fatura->numero,
            'recibo_numero' => $reciboNumero,
            'imobiliaria_id' => $fatura->imobiliaria_id,
            'aprovada_por' => $adminId,
        ]);

        // Notificação in-app para a imobiliária
        ImobiliariaNotificacao::criar(
            $fatura->imobiliaria_id,
            'fatura_aprovada',
            'Pagamento Confirmado',
            "Fatura {$fatura->numero} — pagamento aprovado! Recibo {$reciboNumero} disponível para download.",
            rtrim(config('app.frontend_url', ''), '/') . '/painel/faturas/' . $fatura->id
        );

        return $fatura->fresh();
    }

    private function ativarPlano(Fatura $fatura): void
    {
        $pagamento = $fatura->pagamento;
        if (!$pagamento) return;

        $subscricao = $pagamento->subscricao;
        if (!$subscricao) return;

        $plano = $subscricao->plano;
        if (!$plano) return;

        // Desativar planos anteriores
        ImobiliariaPlano::where('imobiliaria_id', $fatura->imobiliaria_id)
            ->where('id', '!=', $subscricao->id)
            ->where('ativo', true)
            ->update(['ativo' => false, 'estado' => 'inativa']);

        // Ativar plano atual
        $subscricao->update([
            'data_inicio' => now()->toDateString(),
            'data_expiracao' => now()->addDays($plano->dias_validade)->toDateString(),
            'ativo' => true,
            'estado' => 'ativa',
        ]);
    }
}

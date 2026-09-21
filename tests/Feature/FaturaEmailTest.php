<?php

namespace Tests\Feature;

use App\Mail\FaturaEmitidaMail;
use App\Mail\ComprovativoSubmetidoMail;
use App\Mail\PagamentoConfirmadoMail;
use App\Mail\ComprovativoRejeitadoMail;
use App\Mail\FaturaCanceladaMail;
use App\Models\Admin;
use App\Models\Fatura;
use App\Models\Imobiliaria;
use App\Models\ImobiliariaPlano;
use App\Models\Plano;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FaturaEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_fatura_emitida_envia_email(): void
    {
        Mail::fake();

        $imob = Imobiliaria::where('estado', 'aprovada')->first();
        $plano = Plano::first();

        $subscricao = ImobiliariaPlano::create([
            'imobiliaria_id' => $imob->id,
            'plano_id' => $plano->id,
            'posts_usados' => 0,
            'data_inicio' => null,
            'data_expiracao' => null,
            'ativo' => false,
            'estado' => 'pendente',
        ]);

        $pagamento = \App\Models\Pagamento::create([
            'subscricao_id' => $subscricao->id,
            'imobiliaria_id' => $imob->id,
            'valor' => $plano->preco,
            'moeda' => 'Kz',
            'metodo' => 'transferencia',
            'estado' => 'confirmado',
        ]);

        $fatura = Fatura::create([
            'numero' => Fatura::proximoNumero(),
            'pagamento_id' => $pagamento->id,
            'imobiliaria_id' => $imob->id,
            'valor' => $plano->preco,
            'moeda' => 'Kz',
            'iva' => 0,
            'total' => $plano->preco,
            'subtotal' => $plano->preco,
            'desconto' => 0,
            'retencao' => 0,
            'estado' => 'pendente',
            'emitida_em' => now(),
        ]);

        $action = app(\App\Actions\Faturas\EmitirFaturaAction::class);
        $action->execute($fatura);

        Mail::assertQueued(FaturaEmitidaMail::class, function ($mail) use ($imob) {
            return $mail->hasTo($imob->email);
        });
    }

    public function test_pagamento_confirmado_envia_email_com_recibo(): void
    {
        Mail::fake();

        $imob = Imobiliaria::where('estado', 'aprovada')->first();
        $plano = Plano::first();
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();

        $subscricao = ImobiliariaPlano::create([
            'imobiliaria_id' => $imob->id,
            'plano_id' => $plano->id,
            'posts_usados' => 0,
            'data_inicio' => null,
            'data_expiracao' => null,
            'ativo' => false,
            'estado' => 'pendente',
        ]);

        $pagamento = \App\Models\Pagamento::create([
            'subscricao_id' => $subscricao->id,
            'imobiliaria_id' => $imob->id,
            'valor' => $plano->preco,
            'moeda' => 'Kz',
            'metodo' => 'transferencia',
            'estado' => 'confirmado',
        ]);

        $fatura = Fatura::create([
            'numero' => Fatura::proximoNumero(),
            'pagamento_id' => $pagamento->id,
            'imobiliaria_id' => $imob->id,
            'valor' => $plano->preco,
            'moeda' => 'Kz',
            'iva' => 0,
            'total' => $plano->preco,
            'subtotal' => $plano->preco,
            'desconto' => 0,
            'retencao' => 0,
            'estado' => 'aguarda_aprovacao',
            'emitida_em' => now(),
            'comprovativo_path' => 'comprovativos/test.jpg',
            'comprovativo_enviado_em' => now(),
            'comprovativo_por' => $imob->id,
        ]);

        $action = app(\App\Actions\Faturas\AprovarPagamentoAction::class);
        $action->execute($fatura, $admin->id);

        Mail::assertQueued(PagamentoConfirmadoMail::class, function ($mail) use ($imob) {
            return $mail->hasTo($imob->email);
        });
    }

    public function test_rejeitar_pagamento_envia_email(): void
    {
        Mail::fake();

        $imob = Imobiliaria::where('estado', 'aprovada')->first();
        $plano = Plano::first();
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();

        $subscricao = ImobiliariaPlano::create([
            'imobiliaria_id' => $imob->id,
            'plano_id' => $plano->id,
            'posts_usados' => 0,
            'data_inicio' => null,
            'data_expiracao' => null,
            'ativo' => false,
            'estado' => 'pendente',
        ]);

        $pagamento = \App\Models\Pagamento::create([
            'subscricao_id' => $subscricao->id,
            'imobiliaria_id' => $imob->id,
            'valor' => $plano->preco,
            'moeda' => 'Kz',
            'metodo' => 'transferencia',
            'estado' => 'confirmado',
        ]);

        $fatura = Fatura::create([
            'numero' => Fatura::proximoNumero(),
            'pagamento_id' => $pagamento->id,
            'imobiliaria_id' => $imob->id,
            'valor' => $plano->preco,
            'moeda' => 'Kz',
            'iva' => 0,
            'total' => $plano->preco,
            'subtotal' => $plano->preco,
            'desconto' => 0,
            'retencao' => 0,
            'estado' => 'aguarda_aprovacao',
            'emitida_em' => now(),
            'comprovativo_path' => 'comprovativos/test.jpg',
            'comprovativo_enviado_em' => now(),
            'comprovativo_por' => $imob->id,
        ]);

        $action = app(\App\Actions\Faturas\RejeitarPagamentoAction::class);
        $action->execute($fatura, $admin->id, 'Documento ilegível');

        Mail::assertQueued(ComprovativoRejeitadoMail::class, function ($mail) use ($imob) {
            return $mail->hasTo($imob->email);
        });
    }
}

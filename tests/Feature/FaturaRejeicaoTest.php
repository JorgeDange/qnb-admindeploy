<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Fatura;
use App\Models\Imobiliaria;
use App\Models\ImobiliariaPlano;
use App\Models\Plano;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FaturaRejeicaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_rejeitar_pagamento_guarda_motivo(): void
    {
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
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.faturas.rejeitar', $fatura), [
                'motivo' => 'Comprovativo ilegível',
            ]);

        $fatura->refresh();
        $this->assertEquals('rejeitada', $fatura->estado);
        $this->assertEquals('Comprovativo ilegível', $fatura->motivo_rejeicao);
        $this->assertNotNull($fatura->rejeitada_por);
        $this->assertNotNull($fatura->rejeitada_em);
    }

    public function test_apenas_faturas_aguarda_aprovacao_podem_ser_rejeitadas(): void
    {
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
            'estado' => 'pendente',
            'emitida_em' => now(),
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.faturas.rejeitar', $fatura), [
                'motivo' => 'Teste',
            ]);

        $fatura->refresh();
        $this->assertEquals('pendente', $fatura->estado);
    }

    public function test_fatura_cancelada_nao_pode_ser_aprovada(): void
    {
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
            'estado' => 'cancelada',
            'emitida_em' => now(),
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.faturas.aprovar', $fatura));

        $fatura->refresh();
        $this->assertEquals('cancelada', $fatura->estado);
    }
}

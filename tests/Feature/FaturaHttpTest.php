<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Fatura;
use App\Models\Imobiliaria;
use App\Models\ImobiliariaPlano;
use App\Models\Plano;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaturaHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ==================== ADMIN ROUTES (qnb-admin) ====================

    public function test_admin_index_faturas(): void
    {
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.faturas'));

        $response->assertStatus(200);
        $response->assertSee('Faturas');
    }

    public function test_admin_index_faturas_filtro_estado(): void
    {
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.faturas', ['estado' => 'pendente']));

        $response->assertStatus(200);
    }

    public function test_admin_show_fatura(): void
    {
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();
        $fatura = Fatura::first();

        if (!$fatura) {
            $this->markTestSkipped('Sem faturas.');
        }

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.faturas.show', $fatura));

        $response->assertStatus(200);
        $response->assertSee($fatura->numero);
    }

    public function test_admin_download_fatura(): void
    {
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();
        $fatura = Fatura::first();

        if (!$fatura) {
            $this->markTestSkipped('Sem faturas.');
        }

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.faturas.download', $fatura));

        $response->assertStatus(200);
    }

    public function test_admin_rejeitar_fatura_invalida(): void
    {
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();
        $fatura = Fatura::where('estado', 'pendente')->first();

        if (!$fatura) {
            $this->markTestSkipped('Sem faturas pendentes.');
        }

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.faturas.rejeitar', $fatura), [
                'motivo' => 'Teste',
            ]);

        $response->assertRedirect();
        $fatura->refresh();
        $this->assertEquals('pendente', $fatura->estado);
    }

    public function test_admin_cancelar_fatura(): void
    {
        $admin = Admin::where('email', 'admin@qnbangola.com')->first();
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

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.faturas.cancelar', $fatura));

        $response->assertRedirect();
        $fatura->refresh();
        $this->assertEquals('cancelada', $fatura->estado);
    }
}

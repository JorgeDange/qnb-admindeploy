<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Imovel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminImovelTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = Admin::where('email', 'admin@qnbangola.com')->firstOrFail();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_lista_imoveis(): void
    {
        $this->get(route('admin.imoveis'))->assertStatus(200);
    }

    public function test_detalhe_imovel(): void
    {
        $imovel = Imovel::firstOrFail();
        $this->get(route('admin.imoveis.show', $imovel->id))->assertStatus(200);
    }

    public function test_aprovar_imovel(): void
    {
        $imovel = Imovel::where('estado', 'aprovado')->firstOrFail();

        $this->post(route('admin.imoveis.aprovar', $imovel->id))
            ->assertRedirect();

        $this->assertDatabaseHas('imoveis', [
            'id' => $imovel->id,
            'estado' => 'aprovado',
        ]);
    }

    public function test_rejeitar_imovel(): void
    {
        $imovel = Imovel::where('estado', 'aprovado')->firstOrFail();

        $this->post(route('admin.imoveis.rejeitar', $imovel->id))
            ->assertRedirect();

        $this->assertDatabaseHas('imoveis', [
            'id' => $imovel->id,
            'estado' => 'rejeitado',
        ]);
    }

    public function test_bulk_aprovar_imoveis(): void
    {
        $imovel = Imovel::where('estado', 'aprovado')->firstOrFail();

        $this->post(route('admin.bulk.imoveis'), [
            'ids' => [$imovel->id],
            'acao' => 'aprovar',
        ])->assertRedirect();

        $this->assertDatabaseHas('imoveis', [
            'id' => $imovel->id,
            'estado' => 'aprovado',
        ]);
    }

    public function test_bulk_rejeitar_imoveis(): void
    {
        $imovel = Imovel::where('estado', 'aprovado')->firstOrFail();

        $this->post(route('admin.bulk.imoveis'), [
            'ids' => [$imovel->id],
            'acao' => 'rejeitar',
        ])->assertRedirect();

        $this->assertDatabaseHas('imoveis', [
            'id' => $imovel->id,
            'estado' => 'rejeitado',
        ]);
    }

    public function test_bulk_sem_ids_falha(): void
    {
        $this->post(route('admin.bulk.imoveis'), [
            'ids' => [],
            'acao' => 'aprovar',
        ])->assertSessionHasErrors('ids');
    }
}

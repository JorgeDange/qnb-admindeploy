<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Imobiliaria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminImobiliariaTest extends TestCase
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

    public function test_lista_imobiliarias(): void
    {
        $this->get(route('admin.imobiliarias'))->assertStatus(200);
    }

    public function test_detalhe_imobiliaria(): void
    {
        $imob = Imobiliaria::firstOrFail();
        $this->get(route('admin.imobiliarias.show', $imob->id))->assertStatus(200);
    }

    public function test_aprovar_imobiliaria(): void
    {
        $imob = Imobiliaria::where('estado', 'pendente')->first();

        if (!$imob) {
            $imob = Imobiliaria::factory()->create(['estado' => 'pendente']);
        }

        $this->post(route('admin.imobiliarias.aprovar', $imob->id))
            ->assertRedirect();

        $this->assertDatabaseHas('imobiliarias', [
            'id' => $imob->id,
            'estado' => 'aprovada',
        ]);
    }

    public function test_suspender_imobiliaria(): void
    {
        $imob = Imobiliaria::where('estado', 'aprovada')->firstOrFail();

        $this->post(route('admin.imobiliarias.suspender', $imob->id), [
            'motivo' => 'Teste de suspensão',
        ])->assertRedirect();

        $this->assertDatabaseHas('imobiliarias', [
            'id' => $imob->id,
            'estado' => 'suspensa',
        ]);
    }

    public function test_reativar_imobiliaria(): void
    {
        $imob = Imobiliaria::where('estado', 'aprovada')->firstOrFail();

        $this->post(route('admin.imobiliarias.suspender', $imob->id), [
            'motivo' => 'Suspender para reativar',
        ]);

        $this->post(route('admin.imobiliarias.reativar', $imob->id))
            ->assertRedirect();

        $this->assertDatabaseHas('imobiliarias', [
            'id' => $imob->id,
            'estado' => 'aprovada',
        ]);
    }

    public function test_bloquear_imobiliaria(): void
    {
        $imob = Imobiliaria::where('estado', 'aprovada')->firstOrFail();

        $this->post(route('admin.imobiliarias.bloquear', $imob->id))
            ->assertRedirect();

        $this->assertDatabaseHas('imobiliarias', [
            'id' => $imob->id,
            'estado' => 'bloqueada',
        ]);
    }

    public function test_bulk_aprovar(): void
    {
        $imob = Imobiliaria::factory()->create(['estado' => 'pendente']);

        $this->post(route('admin.bulk.imobiliarias'), [
            'ids' => [$imob->id],
            'acao' => 'aprovar',
        ])->assertRedirect();

        $this->assertDatabaseHas('imobiliarias', [
            'id' => $imob->id,
            'estado' => 'aprovada',
        ]);
    }

    public function test_bulk_sem_ids_falha(): void
    {
        $this->post(route('admin.bulk.imobiliarias'), [
            'ids' => [],
            'acao' => 'aprovar',
        ])->assertSessionHasErrors('ids');
    }
}

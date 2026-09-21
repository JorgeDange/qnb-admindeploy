<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Pagamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagamentoTest extends TestCase
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

    public function test_lista_pagamentos(): void
    {
        $this->get(route('admin.pagamentos'))->assertStatus(200);
    }

    public function test_detalhe_pagamento(): void
    {
        $pagamento = Pagamento::first();

        if (!$pagamento) {
            $this->markTestSkipped('Sem pagamentos na BD.');
        }

        $this->get(route('admin.pagamentos.show', $pagamento->id))->assertStatus(200);
    }

    public function test_lista_faturas(): void
    {
        $this->get(route('admin.faturas'))->assertStatus(200);
    }
}

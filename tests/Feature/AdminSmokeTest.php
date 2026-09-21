<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_paginas_admin_renderizam(): void
    {
        $this->seed();

        $admin = Admin::where('email', 'admin@qnbangola.com')->firstOrFail();
        $this->actingAs($admin, 'admin');

        // Dashboard e relatorios usam agrupamento em PHP (compativel com SQLite).
        $routes = [
            'admin.dashboard',
            'admin.relatorios',
            'admin.imobiliarias',
            'admin.imoveis',
            'admin.pedidos',
            'admin.denuncias',
            'admin.avaliacoes',
            'admin.mensagens',
            'admin.visitas',
            'admin.planos',
            'admin.subscricoes',
            'admin.pagamentos',
            'admin.faturas',
            'admin.settings',
            'admin.logs',
            'admin.notificacoes',
            'admin.compliance.sessoes',
            'admin.admins',
            'admin.faq',
            'admin.depoimentos',
            'admin.parceiros',
        ];

        foreach ($routes as $name) {
            $this->get(route($name))->assertStatus(200, "Falhou: {$name}");
        }
    }
}

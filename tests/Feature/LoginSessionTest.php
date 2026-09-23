<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_persiste_sessao_e_acede_dashboard(): void
    {
        Admin::create([
            'nome' => 'Admin Teste',
            'email' => 'admin@teste.local',
            'password' => Hash::make('admin1234'),
            'role' => 'super_admin',
            'ativo' => true,
        ]);

        // Sem sessão → /admin vai para login
        $this->get('/admin')->assertRedirect(route('admin.login'));

        // Login com credenciais válidas
        $response = $this->post(route('admin.login.post'), [
            'email' => 'admin@teste.local',
            'password' => 'admin1234',
        ]);
        $response->assertRedirect(route('admin.dashboard'));

        // A sessão tem de persistir: /admin já não redireciona para login
        $this->get('/admin')->assertStatus(200);
        $this->assertTrue(auth('admin')->check());
    }

    public function test_health_reporta_php_e_sessao(): void
    {
        $response = $this->getJson('/health');
        // 200=healthy, 503=degraded (ex.: tabela sessions em falta) — ambos válidos para diagnóstico
        $this->assertContains($response->status(), [200, 503]);

        $json = $response->json();
        $this->assertArrayHasKey('php', $json);
        $this->assertArrayHasKey('session', $json);
        $this->assertArrayHasKey('driver', $json['session']);
        $this->assertArrayHasKey('php_ok', $json['checks']);
        $this->assertArrayHasKey('sessions_table', $json['checks']);
        $this->assertArrayHasKey('sessions_dir', $json['checks']);
    }
}

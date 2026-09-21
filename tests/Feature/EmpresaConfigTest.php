<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\EmpresaConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaConfigTest extends TestCase
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

    public function test_admin_pode_ver_formulario_configuracoes(): void
    {
        $this->get(route('admin.empresa-config'))->assertStatus(200);
    }

    public function test_admin_pode_atualizar_configuracoes(): void
    {
        $this->put(route('admin.empresa-config.update'), [
            'empresa_nome' => 'QNB Teste Actualizada',
            'empresa_nif' => '1234567890',
            'empresa_endereco' => 'Luanda, Teste',
        ])->assertRedirect();

        $this->assertDatabaseHas('empresa_config', [
            'chave' => 'empresa_nome',
            'valor' => 'QNB Teste Actualizada',
        ]);
    }

    public function test_empresa_nome_e_obrigatorio(): void
    {
        $this->put(route('admin.empresa-config.update'), [
            'empresa_nome' => '',
            'empresa_nif' => '1234567890',
            'empresa_endereco' => 'Luanda',
        ])->assertSessionHasErrors('empresa_nome');
    }

    public function test_empresa_nif_e_obrigatorio(): void
    {
        $this->put(route('admin.empresa-config.update'), [
            'empresa_nome' => 'QNB Teste',
            'empresa_nif' => '',
            'empresa_endereco' => 'Luanda',
        ])->assertSessionHasErrors('empresa_nif');
    }

    public function test_config_gravacao_e_cache(): void
    {
        EmpresaConfig::set('teste_chave', 'valor_inicial', 'teste');
        $this->assertEquals('valor_inicial', EmpresaConfig::get('teste_chave'));

        EmpresaConfig::set('teste_chave', 'valor_actualizado', 'teste');
        $this->assertEquals('valor_actualizado', EmpresaConfig::get('teste_chave'));
    }

    public function test_config_get_grupo(): void
    {
        EmpresaConfig::set('grupo_teste_a', 'A', 'grupo_teste');
        EmpresaConfig::set('grupo_teste_b', 'B', 'grupo_teste');

        $grupo = EmpresaConfig::getGrupo('grupo_teste');
        $this->assertEquals('A', $grupo['grupo_teste_a']);
        $this->assertEquals('B', $grupo['grupo_teste_b']);
    }

    public function test_config_retorna_padrao_se_inexistente(): void
    {
        $resultado = EmpresaConfig::get('chave_inexistente_xyz', 'valor_padrao');
        $this->assertEquals('valor_padrao', $resultado);
    }
}

<?php

namespace App\Services;

use App\Models\EmpresaConfig;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmpresaConfigService
{
    /**
     * Buscar todas as configurações agrupadas.
     */
    public function todas(): array
    {
        $configs = EmpresaConfig::all()->keyBy('chave');
        $grupos = [];

        foreach ($configs as $config) {
            $grupos[$config->grupo][$config->chave] = $config->valor;
        }

        return $grupos;
    }

    /**
     * Buscar valor de uma chave.
     */
    public function get(string $chave, ?string $padrao = null): ?string
    {
        return EmpresaConfig::get($chave, $padrao);
    }

    /**
     * Definir valor de uma chave.
     */
    public function set(string $chave, ?string $valor, string $grupo = 'geral'): void
    {
        EmpresaConfig::set($chave, $valor, $grupo);
    }

    /**
     * Atualizar múltiplos valores de um grupo.
     */
    public function atualizarGrupo(string $grupo, array $dados): void
    {
        foreach ($dados as $chave => $valor) {
            EmpresaConfig::set($chave, $valor, $grupo);
        }
    }

    /**
     * Upload do logótipo da empresa.
     */
    public function uploadLogo(UploadedFile $file): string
    {
        // Remover logo anterior
        $logoAtual = EmpresaConfig::get('empresa_logo');
        if ($logoAtual && Storage::disk('public')->exists($logoAtual)) {
            Storage::disk('public')->delete($logoAtual);
        }

        $caminho = $file->store('empresa', 'public');
        EmpresaConfig::set('empresa_logo', $caminho, 'identidade');

        return $caminho;
    }

    /**
     * Buscar snapshot dos dados da empresa (para faturas).
     */
    public function snapshot(): array
    {
        return [
            'empresa_nome'      => $this->get('empresa_nome', ''),
            'empresa_nif'       => $this->get('empresa_nif', ''),
            'empresa_endereco'  => $this->get('empresa_endereco', ''),
            'empresa_telefone'  => $this->get('empresa_telefone', ''),
            'empresa_email'     => $this->get('empresa_email', ''),
            'banco_nome'        => $this->get('banco_nome', ''),
            'banco_iban'        => $this->get('banco_iban', ''),
            'banco_titular'     => $this->get('banco_titular', ''),
        ];
    }

    /**
     * Buscar configurações de fatura.
     */
    public function configFatura(): array
    {
        return [
            'iva_percentagem'     => (float) $this->get('fatura_iva_percentagem', '14'),
            'retencao_percentagem' => (float) $this->get('fatura_retencao_percentagem', '0'),
            'dias_vencimento'     => (int) $this->get('fatura_dias_vencimento', '30'),
            'termos'              => $this->get('fatura_termos', ''),
            'rodape'              => $this->get('fatura_rodape', ''),
        ];
    }
}

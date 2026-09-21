<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\EmpresaConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpresaConfigController extends Controller
{
    public function __construct(
        private EmpresaConfigService $configService,
    ) {}

    public function edit()
    {
        $configs = $this->configService->todas();
        return view('admin.empresa-config.edit', compact('configs'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Identificação
            'empresa_nome'       => 'required|string|max:200',
            'empresa_nif'        => 'required|string|max:20',
            'empresa_endereco'   => 'required|string|max:500',
            // Contacto
            'empresa_telefone'   => 'nullable|string|max:30',
            'empresa_email'      => 'nullable|email|max:150',
            'empresa_website'    => 'nullable|url|max:200',
            // Bancário
            'banco_nome'         => 'nullable|string|max:100',
            'banco_iban'         => 'nullable|string|max:60',
            'banco_conta'        => 'nullable|string|max:30',
            'banco_titular'      => 'nullable|string|max:200',
            'banco_nif'          => 'nullable|string|max:20',
            // Fatura
            'fatura_iva_percentagem'       => 'nullable|numeric|min:0|max:100',
            'fatura_retencao_percentagem'  => 'nullable|numeric|min:0|max:100',
            'fatura_dias_vencimento'       => 'nullable|integer|min:1|max:365',
            'fatura_termos'                => 'nullable|string|max:1000',
            'fatura_rodape'                => 'nullable|string|max:500',
        ]);

        $this->configService->atualizarGrupo('identificacao', [
            'empresa_nome'     => $validated['empresa_nome'],
            'empresa_nif'      => $validated['empresa_nif'],
            'empresa_endereco' => $validated['empresa_endereco'],
        ]);

        $this->configService->atualizarGrupo('contacto', [
            'empresa_telefone' => $validated['empresa_telefone'] ?? '',
            'empresa_email'    => $validated['empresa_email'] ?? '',
            'empresa_website'  => $validated['empresa_website'] ?? '',
        ]);

        $this->configService->atualizarGrupo('bancario', [
            'banco_nome'    => $validated['banco_nome'] ?? '',
            'banco_iban'    => $validated['banco_iban'] ?? '',
            'banco_conta'   => $validated['banco_conta'] ?? '',
            'banco_titular' => $validated['banco_titular'] ?? '',
            'banco_nif'     => $validated['banco_nif'] ?? '',
        ]);

        $this->configService->atualizarGrupo('fatura', [
            'fatura_iva_percentagem'       => $validated['fatura_iva_percentagem'] ?? '14',
            'fatura_retencao_percentagem'  => $validated['fatura_retencao_percentagem'] ?? '0',
            'fatura_dias_vencimento'       => $validated['fatura_dias_vencimento'] ?? '30',
            'fatura_termos'                => $validated['fatura_termos'] ?? '',
            'fatura_rodape'                => $validated['fatura_rodape'] ?? '',
        ]);

        ActivityLogService::log('editar_empresa_config', null, [
            'admin_id' => Auth::guard('admin')->id(),
            'campos_alterados' => array_keys($validated),
        ]);

        return back()->with('success', 'Configurações da empresa atualizadas com sucesso.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|file|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $this->configService->uploadLogo($request->file('logo'));

        return back()->with('success', 'Logótipo atualizado com sucesso.');
    }
}

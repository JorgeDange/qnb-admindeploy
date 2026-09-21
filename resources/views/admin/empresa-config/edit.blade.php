@extends('admin.layouts.admin')

@section('title', 'Configuracoes da Empresa — Admin')
@section('pageTitle', 'Configuracoes da Empresa')

@section('content')
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Dados da Empresa</h3>

    <form action="{{ route('admin.empresa-config.update') }}" method="POST" class="ul-painel-form">
        @csrf
        @method('PUT')

        <div class="ul-painel-form-seccao">
            <h4>Identificacao</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nome da Empresa</label>
                        <input type="text" name="empresa_nome" value="{{ old('empresa_nome', $configs['identificacao']['empresa_nome'] ?? '') }}" required>
                        @error('empresa_nome') <span class="ul-painel-erro">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>NIF</label>
                        <input type="text" name="empresa_nif" value="{{ old('empresa_nif', $configs['identificacao']['empresa_nif'] ?? '') }}" required>
                        @error('empresa_nif') <span class="ul-painel-erro">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Endereco</label>
                        <input type="text" name="empresa_endereco" value="{{ old('empresa_endereco', $configs['identificacao']['empresa_endereco'] ?? '') }}" required>
                        @error('empresa_endereco') <span class="ul-painel-erro">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="ul-painel-form-seccao">
            <h4>Contacto</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="empresa_telefone" value="{{ old('empresa_telefone', $configs['contacto']['empresa_telefone'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="empresa_email" value="{{ old('empresa_email', $configs['contacto']['empresa_email'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Website</label>
                        <input type="url" name="empresa_website" value="{{ old('empresa_website', $configs['contacto']['empresa_website'] ?? '') }}" placeholder="https://...">
                    </div>
                </div>
            </div>
        </div>

        <div class="ul-painel-form-seccao">
            <h4>Dados Bancarios</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Banco</label>
                        <input type="text" name="banco_nome" value="{{ old('banco_nome', $configs['bancario']['banco_nome'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>IBAN</label>
                        <input type="text" name="banco_iban" value="{{ old('banco_iban', $configs['bancario']['banco_iban'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Conta</label>
                        <input type="text" name="banco_conta" value="{{ old('banco_conta', $configs['bancario']['banco_conta'] ?? '') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Titular da Conta</label>
                        <input type="text" name="banco_titular" value="{{ old('banco_titular', $configs['bancario']['banco_titular'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NIF Bancario</label>
                        <input type="text" name="banco_nif" value="{{ old('banco_nif', $configs['bancario']['banco_nif'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="ul-painel-form-seccao">
            <h4>Configuracoes de Fatura</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>IVA (%)</label>
                        <input type="number" name="fatura_iva_percentagem" value="{{ old('fatura_iva_percentagem', $configs['fatura']['fatura_iva_percentagem'] ?? '14') }}" min="0" max="100" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Retencao na Fonte (%)</label>
                        <input type="number" name="fatura_retencao_percentagem" value="{{ old('fatura_retencao_percentagem', $configs['fatura']['fatura_retencao_percentagem'] ?? '0') }}" min="0" max="100" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dias Vencimento</label>
                        <input type="number" name="fatura_dias_vencimento" value="{{ old('fatura_dias_vencimento', $configs['fatura']['fatura_dias_vencimento'] ?? '30') }}" min="1" max="365">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Termos de Pagamento</label>
                        <textarea name="fatura_termos" rows="3">{{ old('fatura_termos', $configs['fatura']['fatura_termos'] ?? '') }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rodape da Fatura</label>
                        <textarea name="fatura_rodape" rows="3">{{ old('fatura_rodape', $configs['fatura']['fatura_rodape'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="ul-btn">Salvar Configuracoes</button>
    </form>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', ($plano ? 'Editar' : 'Novo') . ' Plano — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">{{ $plano ? 'Editar Plano' : 'Novo Plano' }}</h1>
    <a href="{{ route('admin.planos') }}" class="ul-painel-btn ul-painel-btn--cinza">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<div class="ul-painel-card" style="max-width:700px;">
    <form action="{{ $plano ? route('admin.planos.atualizar', $plano) : route('admin.planos.salvar') }}" method="POST">
        @csrf
        @if($plano)
            @method('PUT')
        @endif

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Nome *</label>
            <input type="text" name="nome" class="ul-painel-form-input" value="{{ old('nome', $plano->nome ?? '') }}" required>
            @error('nome')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Descrição</label>
            <textarea name="descricao" class="ul-painel-form-input" rows="3">{{ old('descricao', $plano->descricao ?? '') }}</textarea>
            @error('descricao')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Limite de Posts *</label>
                <input type="number" name="posts_limite" class="ul-painel-form-input" value="{{ old('posts_limite', $plano->posts_limite ?? 5) }}" min="1" required>
                @error('posts_limite')
                    <span class="ul-painel-form-erro">{{ $message }}</span>
                @enderror
            </div>

            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Dias de Validade *</label>
                <input type="number" name="dias_validade" class="ul-painel-form-input" value="{{ old('dias_validade', $plano->dias_validade ?? 30) }}" min="1" required>
                @error('dias_validade')
                    <span class="ul-painel-form-erro">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Preço *</label>
                <input type="number" name="preco" class="ul-painel-form-input" value="{{ old('preco', $plano->preco ?? 0) }}" min="0" step="0.01" required>
                @error('preco')
                    <span class="ul-painel-form-erro">{{ $message }}</span>
                @enderror
            </div>

            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Moeda *</label>
                <select name="moeda" class="ul-painel-form-input" required>
                    <option value="Kz" {{ old('moeda', $plano->moeda ?? 'Kz') === 'Kz' ? 'selected' : '' }}>Kz (Kwanza)</option>
                    <option value="USD" {{ old('moeda', $plano->moeda ?? '') === 'USD' ? 'selected' : '' }}>USD (Dólar)</option>
                </select>
                @error('moeda')
                    <span class="ul-painel-form-erro">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Ordem</label>
            <input type="number" name="ordem" class="ul-painel-form-input" value="{{ old('ordem', $plano->ordem ?? 0) }}" min="0">
        </div>

        <div style="display:flex;gap:20px;margin-top:12px;">
            <label class="ul-painel-form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" name="ativo" value="1" {{ old('ativo', $plano->ativo ?? true) ? 'checked' : '' }}>
                Ativo
            </label>
            <label class="ul-painel-form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" name="destaque" value="1" {{ old('destaque', $plano->destaque ?? false) ? 'checked' : '' }}>
                Destaque
            </label>
        </div>

        <div style="margin-top:24px;">
            <button type="submit" class="ul-painel-btn ul-painel-btn--primario">
                <i class="bi bi-check-lg"></i> {{ $plano ? 'Guardar Alterações' : 'Criar Plano' }}
            </button>
        </div>
    </form>
</div>
@endsection

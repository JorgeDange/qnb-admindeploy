@extends('admin.layouts.admin')

@section('title', ($subscricao ? 'Editar' : 'Nova') . ' Subscrição — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">{{ $subscricao ? 'Editar Subscrição' : 'Nova Subscrição' }}</h1>
    <a href="{{ route('admin.subscricoes') }}" class="ul-painel-btn ul-painel-btn--cinza">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<div class="ul-painel-card" style="max-width:700px;">
    <form action="{{ $subscricao ? route('admin.subscricoes.atualizar', $subscricao) : route('admin.subscricoes.salvar') }}" method="POST">
        @csrf
        @if($subscricao)
            @method('PUT')
        @endif

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Imobiliária *</label>
            <select name="imobiliaria_id" class="ul-painel-form-input" required>
                <option value="">Selecione...</option>
                @foreach($imobiliarias as $imob)
                    <option value="{{ $imob->id }}" {{ old('imobiliaria_id', $subscricao?->imobiliaria_id ?? '') == $imob->id ? 'selected' : '' }}>
                        {{ $imob->nome }}
                    </option>
                @endforeach
            </select>
            @error('imobiliaria_id')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Plano *</label>
            <select name="plano_id" class="ul-painel-form-input" required>
                <option value="">Selecione...</option>
                @foreach($planos as $p)
                    <option value="{{ $p->id }}" {{ old('plano_id', $subscricao?->plano_id ?? '') == $p->id ? 'selected' : '' }}>
                        {{ $p->nome }} — {{ number_format($p->preco, 0, ',', '.') }} {{ $p->moeda }} ({{ $p->posts_limite }} posts, {{ $p->dias_validade }} dias)
                    </option>
                @endforeach
            </select>
            @error('plano_id')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Data de Início *</label>
                <input type="date" name="data_inicio" class="ul-painel-form-input" value="{{ old('data_inicio', $subscricao?->data_inicio?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                @error('data_inicio')
                    <span class="ul-painel-form-erro">{{ $message }}</span>
                @enderror
            </div>

            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Data de Expiração *</label>
                <input type="date" name="data_expiracao" class="ul-painel-form-input" value="{{ old('data_expiracao', $subscricao?->data_expiracao?->format('Y-m-d') ?? date('Y-m-d', strtotime('+30 days'))) }}" required>
                @error('data_expiracao')
                    <span class="ul-painel-form-erro">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Posts Usados</label>
                <input type="number" name="posts_usados" class="ul-painel-form-input" value="{{ old('posts_usados', $subscricao?->posts_usados ?? 0) }}" min="0">
            </div>

            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Estado *</label>
                <select name="estado" class="ul-painel-form-input" required>
                    @foreach(['ativa', 'pendente', 'expirada', 'cancelada'] as $est)
                        <option value="{{ $est }}" {{ old('estado', $subscricao?->estado ?? 'ativa') === $est ? 'selected' : '' }}>
                            {{ ucfirst($est) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-top:12px;">
            <label class="ul-painel-form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" name="renovacao_automatica" value="1" {{ old('renovacao_automatica', $subscricao?->renovacao_automatica ?? false) ? 'checked' : '' }}>
                Renovação automática
            </label>
        </div>

        <div style="margin-top:24px;">
            <button type="submit" class="ul-painel-btn ul-painel-btn--primario">
                <i class="bi bi-check-lg"></i> {{ $subscricao ? 'Guardar Alterações' : 'Criar Subscrição' }}
            </button>
        </div>
    </form>
</div>
@endsection

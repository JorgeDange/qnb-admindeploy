@extends('admin.layouts.admin')

@section('title', 'Pedido #' . $pedido->id . ' - Admin')
@section('pageTitle', 'Pedido #' . $pedido->id)

@section('content')
<div class="ul-painel-head">
    <div>
        <h2>Pedido #{{ $pedido->id }}</h2>
    </div>
    <a href="{{ route('admin.pedidos') }}" class="ul-btn ul-btn--sm">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<!-- info card -->
<div class="ul-painel-card mb-3">
    <div class="ul-painel-card-titulo">
        Informações do Pedido
        <span class="ul-badge ul-badge--{{ $pedido->estado === 'fechado' ? 'aprovado' : ($pedido->estado === 'perdido' ? 'rejeitado' : 'pendente') }} ms-2">
            {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
        </span>
    </div>

    <div class="ul-painel-grid-dados">
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Imobiliária</span>
            <span class="ul-painel-dado-valor">{{ $pedido->imobiliaria->nome ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Email</span>
            <span class="ul-painel-dado-valor">{{ $pedido->imobiliaria->email ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Telefone</span>
            <span class="ul-painel-dado-valor">{{ $pedido->imobiliaria->telefone ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Plano Pretendido</span>
            <span class="ul-painel-dado-valor">{{ $pedido->plano_pretendido ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Estado</span>
            <span class="ul-painel-dado-valor">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Data</span>
            <span class="ul-painel-dado-valor">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    @if($pedido->mensagem)
    <div class="ul-painel-aviso ul-painel-aviso--cinza m-3">
        <strong>Mensagem:</strong>
        <p class="mb-0 mt-1">{{ $pedido->mensagem }}</p>
    </div>
    @endif
</div>

<!-- alterar estado -->
<div class="ul-painel-card mb-3">
    <div class="ul-painel-card-titulo">Alterar Estado</div>
    <form action="{{ route('admin.pedidos.estado', $pedido->id) }}" method="POST" class="ul-painel-form p-3">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Novo Estado</label>
                    <select name="estado" required>
                        <option value="novo" {{ $pedido->estado === 'novo' ? 'selected' : '' }}>Novo</option>
                        <option value="em_negociacao" {{ $pedido->estado === 'em_negociacao' ? 'selected' : '' }}>Em Negociação</option>
                        <option value="proposta_enviada" {{ $pedido->estado === 'proposta_enviada' ? 'selected' : '' }}>Proposta Enviada</option>
                        <option value="fechado" {{ $pedido->estado === 'fechado' ? 'selected' : '' }}>Fechado</option>
                        <option value="perdido" {{ $pedido->estado === 'perdido' ? 'selected' : '' }}>Perdido</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="ul-btn ul-btn--primary w-100">Salvar</button>
            </div>
        </div>
    </form>
</div>

<!-- liberar plano -->
<div class="ul-painel-card mb-3">
    <div class="ul-painel-card-titulo">Liberar Plano</div>
    <form id="pedido-liberar-{{ $pedido->id }}" action="{{ route('admin.pedidos.liberar', $pedido->id) }}" method="POST" class="ul-painel-form p-3">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Plano</label>
                    <select name="plano_id" required>
                        <option value="">Selecionar plano...</option>
                        @foreach($planos ?? [] as $plano)
                        <option value="{{ $plano->id }}">{{ $plano->nome }} — {{ number_format($plano->preco, 0, ',', '.') }} {{ $plano->moeda }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Dias de Validade</label>
                    <input type="number" name="dias_validade" value="30" min="1" required>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="ul-btn ul-btn--primary w-100" onclick="modalConfirmar('Liberar Plano', 'Liberar este plano para a imobiliária?', function(){ document.getElementById('pedido-liberar-{{ $pedido->id }}').submit(); })">
                    Liberar Plano
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
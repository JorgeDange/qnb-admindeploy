@extends('admin.layouts.admin')

@section('title', 'Pedidos - Admin')
@section('pageTitle', 'Pedidos')

@section('content')
<div class="ul-painel-head">
    <h2>Pedidos</h2>
</div>

<!-- stat cards -->
<div class="ul-painel-stats">
    <a href="{{ route('admin.pedidos', ['estado' => 'novo']) }}" class="ul-painel-stat" style="text-decoration:none;">
        <span class="ul-painel-stat-numero">{{ $stats['novo'] ?? 0 }}</span>
        <span class="ul-painel-stat-rotulo">Novos</span>
    </a>
    <a href="{{ route('admin.pedidos', ['estado' => 'em_negociacao']) }}" class="ul-painel-stat" style="text-decoration:none;">
        <span class="ul-painel-stat-numero">{{ $stats['em_negociacao'] ?? 0 }}</span>
        <span class="ul-painel-stat-rotulo">Em Negociação</span>
    </a>
    <a href="{{ route('admin.pedidos', ['estado' => 'proposta_enviada']) }}" class="ul-painel-stat" style="text-decoration:none;">
        <span class="ul-painel-stat-numero">{{ $stats['proposta_enviada'] ?? 0 }}</span>
        <span class="ul-painel-stat-rotulo">Proposta Enviada</span>
    </a>
    <a href="{{ route('admin.pedidos', ['estado' => 'fechado']) }}" class="ul-painel-stat" style="text-decoration:none;border-left:3px solid #10b981;">
        <span class="ul-painel-stat-numero ul-painel-stat-numero--sucesso">{{ $stats['fechado'] ?? 0 }}</span>
        <span class="ul-painel-stat-rotulo">Fechados</span>
    </a>
    <a href="{{ route('admin.pedidos', ['estado' => 'perdido']) }}" class="ul-painel-stat" style="text-decoration:none;border-left:3px solid #ef4444;">
        <span class="ul-painel-stat-numero ul-painel-stat-numero--perigo">{{ $stats['perdido'] ?? 0 }}</span>
        <span class="ul-painel-stat-rotulo">Perdidos</span>
    </a>
</div>

<!-- filter -->
<div class="ul-painel-card mb-3">
    <form method="GET" action="{{ route('admin.pedidos') }}" class="ul-painel-form row g-3 align-items-end p-3">
        <div class="col-md-4">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="novo" {{ request('estado') === 'novo' ? 'selected' : '' }}>Novo</option>
                    <option value="em_negociacao" {{ request('estado') === 'em_negociacao' ? 'selected' : '' }}>Em Negociação</option>
                    <option value="proposta_enviada" {{ request('estado') === 'proposta_enviada' ? 'selected' : '' }}>Proposta Enviada</option>
                    <option value="fechado" {{ request('estado') === 'fechado' ? 'selected' : '' }}>Fechado</option>
                    <option value="perdido" {{ request('estado') === 'perdido' ? 'selected' : '' }}>Perdido</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="ul-btn ul-btn--primary w-100">Filtrar</button>
        </div>
    </form>
</div>

<!-- table -->
<div class="ul-painel-card">
    <div class="ul-painel-card-titulo">
        Lista de Pedidos
        <span class="ul-badge ul-badge--info ms-2">{{ $pedidos->total() }} registo(s)</span>
    </div>

    @if($pedidos->count())
    <div class="ul-painel-lista">
        @foreach($pedidos as $pedido)
        <div class="ul-painel-imovel">
            <div class="ul-painel-imovel-info">
                <div class="ul-painel-imovel-dados">
                    <h4>{{ $pedido->imobiliaria->nome ?? '—' }}</h4>
                    <p class="text-muted mb-0">{{ $pedido->plano_pretendido ?? '—' }}</p>
                </div>
                <div class="ul-painel-imovel-meta">
                    <span class="ul-badge ul-badge--{{ $pedido->estado === 'fechado' ? 'aprovado' : ($pedido->estado === 'perdido' ? 'rejeitado' : 'pendente') }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>
                    <span class="text-muted ms-2">{{ $pedido->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="ul-painel-imovel-acoes">
                <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="ul-btn ul-btn--sm ul-btn--primary">Ver</a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $pedidos->withQueryString()->links() }}
    </div>
    @else
    <div class="ul-painel-vazio">
        <p>Nenhum pedido encontrado.</p>
    </div>
    @endif
</div>
@endsection
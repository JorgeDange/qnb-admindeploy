@extends('admin.layouts.admin')

@section('title', $imobiliaria->nome . ' - Admin')
@section('pageTitle', $imobiliaria->nome)

@section('content')
<!-- back button -->
<div class="ul-painel-head">
    <a href="{{ route('admin.imobiliarias') }}" class="ul-btn ul-btn--sm" style="background:var(--ul-gray);color:var(--ul-secondary);">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<!-- info card -->
<div class="ul-painel-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
        <h2 class="ul-painel-card-titulo">Informações da Imobiliária</h2>
        <span class="ul-badge ul-badge--{{ $imobiliaria->estado === 'aprovada' ? 'aprovado' : ($imobiliaria->estado === 'pendente' ? 'pendente' : 'rejeitado') }}">
            {{ ucfirst($imobiliaria->estado) }}
        </span>
    </div>
    <div class="ul-painel-grid-dados">
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Nome</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->nome }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Email</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->email }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Telefone</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->telefone }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">NIF</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->nif ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Província</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->provincia ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Município</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->municipio ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Data Registo</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<!-- plano atual -->
<div class="ul-painel-card">
    <h2 class="ul-painel-card-titulo" style="margin-bottom:14px;">Plano Atual</h2>
    @if($imobiliaria->planoAtivo ?? null)
    <div class="ul-painel-grid-dados">
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Plano</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->planoAtivo->plano->nome ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Posts Usados</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->planoAtivo->posts_usados ?? 0 }} / {{ $imobiliaria->planoAtivo->plano->posts_limite ?? '∞' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Validade</span>
            <span class="ul-painel-dado-valor">{{ $imobiliaria->planoAtivo->data_expiracao ? $imobiliaria->planoAtivo->data_expiracao->format('d/m/Y') : 'Indefinida' }}</span>
        </div>
    </div>
    @else
    <div class="ul-painel-vazio">
        <p>Nenhum plano ativo.</p>
    </div>
    @endif
</div>

<!-- imóveis -->
<div class="ul-painel-card">
    <h2 class="ul-painel-card-titulo" style="margin-bottom:14px;">Imóveis da Imobiliária</h2>
    @if($imobiliaria->imoveis->count())
        @foreach($imobiliaria->imoveis as $imovel)
        <div class="ul-painel-imovel">
            <div class="ul-painel-imovel-info">
                <span class="ul-painel-imovel-titulo">{{ $imovel->titulo }}</span>
                <span class="ul-painel-imovel-ref">Ref: {{ $imovel->referencia }}</span>
                <div class="ul-painel-imovel-preco">{{ number_format($imovel->preco, 0, ',', '.') }} {{ $imovel->moeda }}</div>
            </div>
            <div class="ul-painel-imovel-acoes">
                <span class="ul-badge ul-badge--{{ $imovel->estado }}">
                    {{ ucfirst($imovel->estado) }}
                </span>
                <a href="{{ route('admin.imoveis.show', $imovel->id) }}" class="ul-btn ul-btn--sm" style="background:var(--ul-primary);color:var(--white);">Ver</a>
            </div>
        </div>
        @endforeach
    @else
    <div class="ul-painel-vazio">
        <p>Esta imobiliária ainda não tem imóveis.</p>
    </div>
    @endif
</div>

<!-- actions -->
<div class="ul-painel-card">
    <h2 class="ul-painel-card-titulo" style="margin-bottom:14px;">Ações</h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        @if($imobiliaria->estado === 'pendente')
        <form id="imob-aprovar-{{ $imobiliaria->id }}" action="{{ route('admin.imobiliarias.aprovar', $imobiliaria->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="button" class="ul-btn ul-btn--sm ul-btn--sucesso" onclick="modalConfirmar('Aprovar Imobiliária', 'Aprovar esta imobiliária?', function(){ document.getElementById('imob-aprovar-{{ $imobiliaria->id }}').submit(); })">Aprovar Imobiliária</button>
        </form>
        @endif
        @if($imobiliaria->estado === 'aprovada')
        <form id="imob-suspender-{{ $imobiliaria->id }}" action="{{ route('admin.imobiliarias.suspender', $imobiliaria->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="button" class="ul-btn ul-btn--sm ul-btn--aviso" onclick="modalPerigo('Suspender Imobiliária', 'Suspender esta imobiliária?', function(){ document.getElementById('imob-suspender-{{ $imobiliaria->id }}').submit(); })">Suspender Imobiliária</button>
        </form>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', $imovel->titulo . ' - Admin')
@section('pageTitle', $imovel->titulo)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.imoveis') }}" class="ul-btn ul-btn--sm">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<!-- info card -->
<div class="ul-painel-card">
    <div class="ul-painel-head">
        <h3 class="ul-painel-card-titulo">Informações do Imóvel</h3>
        <span class="ul-badge ul-badge--{{ $imovel->estado }}">{{ ucfirst($imovel->estado) }}</span>
    </div>
    <div class="ul-painel-grid-dados">
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Referência</span>
            <span class="ul-painel-dado-valor">{{ $imovel->referencia }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Título</span>
            <span class="ul-painel-dado-valor">{{ $imovel->titulo }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Tipo</span>
            <span class="ul-painel-dado-valor">{{ $imovel->tipo }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Preço</span>
            <span class="ul-painel-dado-valor">{{ number_format($imovel->preco, 0, ',', '.') }} {{ $imovel->moeda }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Província</span>
            <span class="ul-painel-dado-valor">{{ $imovel->provincia }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Município</span>
            <span class="ul-painel-dado-valor">{{ $imovel->municipio }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Quartos</span>
            <span class="ul-painel-dado-valor">{{ $imovel->quartos ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Casas de Banho</span>
            <span class="ul-painel-dado-valor">{{ $imovel->casas_banho ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Área</span>
            <span class="ul-painel-dado-valor">{{ $imovel->area ? $imovel->area . ' m²' : '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Imobiliária</span>
            <span class="ul-painel-dado-valor">{{ $imovel->imobiliaria->nome ?? '—' }}</span>
        </div>
        <div class="ul-painel-dado">
            <span class="ul-painel-dado-rotulo">Data Registo</span>
            <span class="ul-painel-dado-valor">{{ $imovel->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    <div style="margin-top:18px;">
        <span class="ul-painel-dado-rotulo">Descrição</span>
        <p style="font-size:14px;color:var(--ul-gray2);margin:6px 0 0;">{{ $imovel->descricao ?? 'Sem descrição' }}</p>
    </div>
</div>

<!-- fotos -->
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Fotos</h3>
    @if($imovel->fotos->count())
    <div class="ul-fotos-preview">
        @foreach($imovel->fotos as $foto)
        <div style="position:relative;display:inline-block;">
            <img src="{{ asset('storage/' . $foto->caminho) }}" alt="{{ $imovel->titulo }}">
            @if($foto->principal)
            <span class="ul-badge ul-badge--destaque" style="position:absolute;top:6px;left:6px;">Principal</span>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="ul-painel-vazio">
        <p>Nenhuma foto disponível.</p>
    </div>
    @endif
</div>

<!-- amenities -->
@if($imovel->amenidades->count())
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Comodidades</h3>
    <div class="ul-amenidades-grid">
        @foreach($imovel->amenidades as $amenity)
        <span class="ul-badge ul-badge--aprovado">{{ $amenity->nome }}</span>
        @endforeach
    </div>
</div>
@endif

<!-- actions -->
@if($imovel->estado === 'pendente')
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Ações</h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <form id="imov-aprovar-{{ $imovel->id }}" action="{{ route('admin.imoveis.aprovar', $imovel->id) }}" method="POST">
            @csrf
            <button type="button" class="ul-btn ul-btn--sm ul-btn--sucesso" onclick="modalConfirmar('Aprovar Imóvel', 'Aprovar este imóvel?', function(){ document.getElementById('imov-aprovar-{{ $imovel->id }}').submit(); })">Aprovar Imóvel</button>
        </form>
        <form id="imov-rejeitar-{{ $imovel->id }}" action="{{ route('admin.imoveis.rejeitar', $imovel->id) }}" method="POST">
            @csrf
            <button type="button" class="ul-btn ul-btn--sm ul-btn--perigo" onclick="modalPerigo('Rejeitar Imóvel', 'Rejeitar este imóvel?', function(){ document.getElementById('imov-rejeitar-{{ $imovel->id }}').submit(); })">Rejeitar Imóvel</button>
        </form>
    </div>
</div>
@else
<div class="ul-painel-aviso ul-painel-aviso--cinza">
    <div class="ul-painel-aviso-texto">
        <p>Este imóvel já foi {{ $imovel->estado }}.</p>
    </div>
</div>
@endif
@endsection

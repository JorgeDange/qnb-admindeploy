@extends('admin.layouts.admin')

@section('title', 'Imóveis - Admin')
@section('pageTitle', 'Imóveis')

@section('content')
<div class="ul-painel-head">
    <div>
        <h2 class="ul-painel-card-titulo">Imóveis</h2>
        <p class="ul-painel-card-subtitulo">{{ $imoveis->total() }} registo(s)</p>
    </div>
</div>

<!-- filters -->
<div class="ul-painel-card">
    <form method="GET" action="{{ route('admin.imoveis') }}" class="ul-painel-form row g-3 align-items-end">
        <div class="col-md-3">
            <div class="form-group">
                <label>Pesquisar</label>
                <input type="text" name="search" placeholder="Ref, título..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('estado') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="aprovado" {{ request('estado') === 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                    <option value="rejeitado" {{ request('estado') === 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Tipo</label>
                <select name="tipo">
                    <option value="">Todos</option>
                    @foreach($tipos ?? [] as $tipo)
                    <option value="{{ $tipo }}" {{ request('tipo') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Província</label>
                <select name="provincia">
                    <option value="">Todas</option>
                    @foreach($provincias ?? [] as $prov)
                    <option value="{{ $prov }}" {{ request('provincia') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="ul-btn ul-btn--sm w-100" style="background: var(--ul-primary); color: #fff;">Filtrar</button>
        </div>
    </form>
</div>

<!-- listing -->
<div class="ul-painel-card">
    @if($imoveis->count())
        @foreach($imoveis as $imovel)
        <div class="ul-painel-imovel">
            @if($imovel->fotos->count())
            <div class="ul-painel-imovel-foto">
                <img src="{{ asset('storage/' . $imovel->fotos->first()->caminho) }}" alt="{{ $imovel->titulo }}">
            </div>
            @endif
            <div class="ul-painel-imovel-info">
                <div>
                    <span class="ul-painel-imovel-ref">Ref: {{ $imovel->referencia }}</span>
                    <span class="ul-badge ul-badge--{{ $imovel->estado }}">{{ ucfirst($imovel->estado) }}</span>
                </div>
                <h4 class="ul-painel-imovel-titulo">{{ $imovel->titulo }}</h4>
                <span class="ul-painel-imovel-local">{{ $imovel->tipo }} &middot; {{ $imovel->municipio }}, {{ $imovel->provincia }}</span>
                <span class="ul-painel-imovel-preco">{{ number_format($imovel->preco, 0, ',', '.') }} {{ $imovel->moeda }}</span>
                <span style="font-size:12.5px;color:var(--ul-gray2);">&middot; {{ $imovel->imobiliaria->nome ?? '—' }}</span>
            </div>
            <div class="ul-painel-imovel-acoes">
                <a href="{{ route('admin.imoveis.show', $imovel->id) }}" class="ul-btn ul-btn--sm" style="background:var(--ul-primary);color:#fff;">Ver</a>
                @if($imovel->estado === 'pendente')
                <form id="imov-aprovar-{{ $imovel->id }}" action="{{ route('admin.imoveis.aprovar', $imovel->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="ul-btn ul-btn--sm ul-btn--sucesso" onclick="modalConfirmar('Aprovar Imóvel', 'Aprovar este imóvel?', function(){ document.getElementById('imov-aprovar-{{ $imovel->id }}').submit(); })">Aprovar</button>
                </form>
                <form id="imov-rejeitar-{{ $imovel->id }}" action="{{ route('admin.imoveis.rejeitar', $imovel->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="ul-btn ul-btn--sm ul-btn--perigo" onclick="modalPerigo('Rejeitar Imóvel', 'Rejeitar este imóvel?', function(){ document.getElementById('imov-rejeitar-{{ $imovel->id }}').submit(); })">Rejeitar</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach

        <div class="mt-3">
            {{ $imoveis->withQueryString()->links() }}
        </div>
    @else
        <div class="ul-painel-vazio">
            <p>Nenhum imóvel encontrado.</p>
        </div>
    @endif
</div>
@endsection

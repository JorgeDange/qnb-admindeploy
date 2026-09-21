@extends('admin.layouts.admin')

@section('title', 'Imobiliárias - Admin')
@section('pageTitle', 'Imobiliárias')

@section('content')
<!-- filters -->
<div class="ul-painel-card">
    <form method="GET" action="{{ route('admin.imobiliarias') }}" class="ul-painel-form row g-3 align-items-end">
        <div class="col-md-6">
            <div class="form-group">
                <label>Pesquisar</label>
                <input type="text" name="search" placeholder="Nome, email ou telefone..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('estado') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="aprovada" {{ request('estado') === 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                    <option value="suspensa" {{ request('estado') === 'suspensa' ? 'selected' : '' }}>Suspensa</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="ul-btn ul-btn--sm w-100" style="background:var(--ul-primary);color:var(--white);">Filtrar</button>
        </div>
    </form>
</div>

<!-- header -->
<div class="ul-painel-head">
    <div>
        <h2 class="ul-painel-card-titulo">Lista de Imobiliárias</h2>
        <p class="ul-painel-card-subtitulo">{{ $imobiliarias->total() }} registo(s)</p>
    </div>
</div>

<!-- list -->
@if($imobiliarias->count())
    @foreach($imobiliarias as $imobiliaria)
    <div class="ul-painel-imovel">
        <div class="ul-painel-imovel-info">
            <span class="ul-painel-imovel-titulo">{{ $imobiliaria->nome }}</span>
            <span class="ul-painel-imovel-ref">ID #{{ $imobiliaria->id }}</span>
            <div style="margin-top:6px;font-size:13px;color:var(--ul-gray2);">
                <span style="margin-right:14px;"><i class="bi bi-envelope" style="color:var(--ul-primary);margin-right:4px;"></i>{{ $imobiliaria->email }}</span>
                <span><i class="bi bi-telephone" style="color:var(--ul-primary);margin-right:4px;"></i>{{ $imobiliaria->telefone }}</span>
            </div>
            <div style="margin-top:4px;font-size:12px;color:var(--ul-gray2);">
                Registo: {{ $imobiliaria->created_at->format('d/m/Y') }}
            </div>
        </div>
        <div class="ul-painel-imovel-acoes">
            <span class="ul-badge ul-badge--{{ $imobiliaria->estado === 'aprovada' ? 'aprovado' : ($imobiliaria->estado === 'pendente' ? 'pendente' : 'rejeitado') }}">
                {{ ucfirst($imobiliaria->estado) }}
            </span>
            <a href="{{ route('admin.imobiliarias.show', $imobiliaria->id) }}" class="ul-btn ul-btn--sm" style="background:var(--ul-primary);color:var(--white);">Ver</a>
            @if($imobiliaria->estado === 'pendente')
            <form id="imob-aprovar-{{ $imobiliaria->id }}" action="{{ route('admin.imobiliarias.aprovar', $imobiliaria->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="button" class="ul-btn ul-btn--sm" style="background:var(--ul-primary);color:var(--white);" onclick="modalConfirmar('Aprovar Imobiliária', 'Aprovar esta imobiliária?', function(){ document.getElementById('imob-aprovar-{{ $imobiliaria->id }}').submit(); })">Aprovar</button>
            </form>
            @endif
            @if($imobiliaria->estado === 'aprovada')
            <form id="imob-suspender-{{ $imobiliaria->id }}" action="{{ route('admin.imobiliarias.suspender', $imobiliaria->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="button" class="ul-btn ul-btn--sm ul-btn--aviso" onclick="modalPerigo('Suspender Imobiliária', 'Suspender esta imobiliária?', function(){ document.getElementById('imob-suspender-{{ $imobiliaria->id }}').submit(); })">Suspender</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach

    <div class="mt-3">
        {{ $imobiliarias->withQueryString()->links() }}
    </div>
@else
    <div class="ul-painel-vazio">
        <p>Nenhuma imobiliária encontrada.</p>
    </div>
@endif
@endsection

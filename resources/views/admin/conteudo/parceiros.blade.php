@extends('admin.layouts.admin')

@section('title', 'Parceiros - Admin')
@section('pageTitle', 'Parceiros')

@section('content')
<!-- form novo parceiro -->
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Novo Parceiro</h3>

    <form action="{{ route('admin.parceiros.salvar') }}" method="POST" enctype="multipart/form-data" class="ul-painel-form">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" value="{{ old('nome') }}" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>URL</label>
                    <input type="url" name="url" value="{{ old('url') }}" placeholder="https://...">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Logótipo</label>
                    <input type="file" name="logo" accept="image/*">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Ativo</label>
                    <select name="ativo">
                        <option value="1" {{ old('ativo', 1) == 1 ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ old('ativo') == 0 ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>
            <div class="col-md-1">
                <button type="submit" class="ul-btn ul-btn--primary w-100">Adicionar</button>
            </div>
        </div>
    </form>
</div>

<!-- lista -->
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Lista de Parceiros</h3>

    @if($parceiros->count())
    @foreach($parceiros as $parceiro)
    <div class="ul-painel-imovel">
        @if($parceiro->logo)
        <div class="ul-painel-imovel-foto">
            <img src="{{ asset('storage/' . $parceiro->logo) }}" alt="{{ $parceiro->nome }}">
        </div>
        @endif
        <div class="ul-painel-imovel-info">
            <div class="ul-painel-imovel-titulo">{{ $parceiro->nome }}</div>
            @if($parceiro->url)
            <span class="ul-painel-imovel-local">
                <a href="{{ $parceiro->url }}" target="_blank" rel="noopener" style="color:var(--ul-primary);">{{ Str::limit($parceiro->url, 50) }}</a>
            </span>
            @endif
        </div>
        <div class="ul-painel-imovel-acoes">
            <span class="ul-badge ul-badge--{{ $parceiro->ativo ? 'aprovado' : 'rejeitado' }}">
                {{ $parceiro->ativo ? 'Ativo' : 'Inativo' }}
            </span>
            <form id="parc-del-{{ $parceiro->id }}" action="{{ route('admin.parceiros.apagar', $parceiro->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="ul-btn ul-btn--sm ul-btn--outline" onclick="modalPerigo('Eliminar Parceiro', 'Eliminar este parceiro?', function(){ document.getElementById('parc-del-{{ $parceiro->id }}').submit(); })">Eliminar</button>
            </form>
        </div>
    </div>
    @endforeach
    @else
    <div class="ul-painel-vazio">
        <p>Nenhum parceiro registado.</p>
    </div>
    @endif
</div>
@endsection

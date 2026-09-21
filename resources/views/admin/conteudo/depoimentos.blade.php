@extends('admin.layouts.admin')

@section('title', 'Depoimentos - Admin')
@section('pageTitle', 'Depoimentos')

@section('content')
<!-- form novo depoimento -->
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Novo Depoimento</h3>

    <form action="{{ route('admin.depoimentos.salvar') }}" method="POST" class="ul-painel-form">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" value="{{ old('nome') }}" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="cargo" value="{{ old('cargo') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Estrelas (1-5)</label>
                    <select name="estrelas">
                        @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ old('estrelas', 5) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Ativo</label>
                    <select name="ativo">
                        <option value="1" {{ old('ativo', 1) == 1 ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ old('ativo') == 0 ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <div class="form-group">
                    <label>Texto</label>
                    <textarea name="texto" rows="3" required>{{ old('texto') }}</textarea>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="ul-btn ul-btn--primary w-100">Adicionar</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- lista -->
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Lista de Depoimentos</h3>

    @if($depoimentos->count())
    @foreach($depoimentos as $depoimento)
    <div class="ul-painel-imovel" style="flex-wrap:wrap;">
        <div class="ul-painel-imovel-info" style="min-width:0;">
            <div class="ul-painel-imovel-titulo">{{ $depoimento->nome }}</div>
            <span class="ul-painel-imovel-local">{{ $depoimento->cargo ?? '—' }}</span>
            <p class="ul-painel-mensagem-texto">{{ Str::limit($depoimento->texto, 120) }}</p>
            <div style="margin-top:6px;">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $depoimento->estrelas)
                        &#9733;
                    @else
                        &#9734;
                    @endif
                @endfor
            </div>
        </div>
        <div class="ul-painel-imovel-acoes">
            <span class="ul-badge ul-badge--{{ $depoimento->ativo ? 'aprovado' : 'rejeitado' }}">
                {{ $depoimento->ativo ? 'Ativo' : 'Inativo' }}
            </span>
            <form id="depo-del-{{ $depoimento->id }}" action="{{ route('admin.depoimentos.apagar', $depoimento->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="ul-btn ul-btn--sm ul-btn--outline" onclick="modalPerigo('Eliminar Depoimento', 'Eliminar este depoimento?', function(){ document.getElementById('depo-del-{{ $depoimento->id }}').submit(); })">Eliminar</button>
            </form>
        </div>
    </div>
    @endforeach
    @else
    <div class="ul-painel-vazio">
        <p>Nenhum depoimento registado.</p>
    </div>
    @endif
</div>
@endsection

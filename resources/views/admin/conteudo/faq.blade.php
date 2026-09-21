@extends('admin.layouts.admin')

@section('title', 'FAQ — Admin')

@section('content')
<div class="ul-painel-head">
    <div>
        <h1 class="ul-painel-card-titulo">FAQ — Perguntas Frequentes</h1>
        <p class="ul-painel-card-subtitulo">Gerir as perguntas frequentes do site público</p>
    </div>
    <button onclick="document.getElementById('faq-form').style.display='block'" class="ul-btn ul-btn--primary"><i class="bi bi-plus-lg"></i> Nova Pergunta</button>
</div>

<div class="ul-painel-card" id="faq-form" style="display:{{ $errors->any() ? 'block' : 'none' }};">
    <h3 class="ul-painel-card-titulo" style="font-size:16px;margin-bottom:14px;">Nova Pergunta</h3>
    <form action="{{ route('admin.faq.salvar') }}" method="POST" class="ul-painel-form">
        @csrf
        <div class="form-group">
            <label for="pergunta">Pergunta</label>
            <input type="text" name="pergunta" id="pergunta" value="{{ old('pergunta') }}" required maxlength="500" placeholder="Ex: Como posso anunciar o meu imóvel?">
        </div>
        <div class="form-group">
            <label for="resposta">Resposta</label>
            <textarea name="resposta" id="resposta" rows="4" required maxlength="5000" placeholder="Resposta detalhada...">{{ old('resposta') }}</textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group">
                <label for="ordem">Ordem</label>
                <input type="number" name="ordem" id="ordem" value="{{ old('ordem', 0) }}" min="0">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <label class="ul-checkbox"><input type="checkbox" name="ativo" value="1" {{ old('ativo', 1) ? 'checked' : '' }}> Ativo</label>
            </div>
        </div>
        <button type="submit" class="ul-btn ul-btn--primary">Guardar</button>
    </form>
</div>

<div class="ul-painel-card">
    @forelse($faqs as $faq)
    <div style="border-bottom:1px solid var(--ul-gray);padding:16px 0;{{ $loop->last ? 'border-bottom:none;' : '' }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                    <span style="font-size:14px;font-weight:700;color:var(--ul-secondary);">{{ $faq->pergunta }}</span>
                    @if(!$faq->ativo)
                        <span class="ul-badge" style="background:rgba(0,0,0,0.06);color:#666;">Inativo</span>
                    @endif
                </div>
                <p style="font-size:13.5px;color:var(--ul-gray2);margin:0;">{{ Str::limit($faq->resposta, 200) }}</p>
                <small style="color:var(--ul-gray2);">Ordem: {{ $faq->ordem }}</small>
            </div>
            <div style="display:flex;gap:6px;flex-shrink:0;">
                <form action="{{ route('admin.faq.toggle', $faq) }}" method="POST">
                    @csrf
                    <button type="submit" class="ul-btn ul-btn--sm ul-btn--outline" title="{{ $faq->ativo ? 'Desativar' : 'Ativar' }}">
                        <i class="bi bi-{{ $faq->ativo ? 'x-lg' : 'check-lg' }}"></i>
                    </button>
                </form>
                <form id="faq-del-{{ $faq->id }}" action="{{ route('admin.faq.apagar', $faq) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="ul-btn ul-btn--sm ul-btn--outline" title="Apagar" onclick="modalPerigo('Apagar FAQ', 'Apagar esta FAQ?', function(){ document.getElementById('faq-del-{{ $faq->id }}').submit(); })"><i class="bi bi-x-lg"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="ul-painel-vazio">Nenhuma pergunta FAQ criada.</div>
    @endforelse
</div>
@endsection

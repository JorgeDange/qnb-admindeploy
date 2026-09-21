@extends('admin.layouts.admin')

@section('title', 'Nova Mensagem — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Nova Mensagem</h1>
    <a href="{{ route('admin.mensagens') }}" class="ul-painel-btn ul-painel-btn--cinza">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<div class="ul-painel-card" style="max-width:700px;">
    <form action="{{ route('admin.mensagens.enviar') }}" method="POST">
        @csrf

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Imobiliária *</label>
            <select name="imobiliaria_id" class="ul-painel-form-input" required>
                <option value="">Selecione...</option>
                @foreach($imobiliarias as $imob)
                    <option value="{{ $imob->id }}" {{ old('imobiliaria_id') == $imob->id ? 'selected' : '' }}>
                        {{ $imob->nome }} — {{ $imob->email }}
                    </option>
                @endforeach
            </select>
            @error('imobiliaria_id')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Assunto *</label>
            <input type="text" name="assunto" class="ul-painel-form-input" value="{{ old('assunto') }}" required>
            @error('assunto')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Mensagem *</label>
            <textarea name="texto" class="ul-painel-form-input" rows="6" required>{{ old('texto') }}</textarea>
            @error('texto')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-top:24px;">
            <button type="submit" class="ul-painel-btn ul-painel-btn--primario">
                <i class="bi bi-send"></i> Enviar Mensagem
            </button>
        </div>
    </form>
</div>
@endsection

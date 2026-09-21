@extends('admin.layouts.admin')

@section('title', 'Thread — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">{{ $thread->assunto }}</h1>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.mensagens') }}" class="ul-painel-btn ul-painel-btn--cinza">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <form action="{{ route('admin.mensagens.fechar', $thread) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="ul-painel-btn ul-painel-btn--aviso">
                <i class="bi bi-x-lg"></i> Fechar Thread
            </button>
        </form>
    </div>
</div>

<div class="ul-painel-card">
    <div style="margin-bottom:12px;color:#888;">
        <strong>Imobiliária:</strong> {{ $thread->imobiliaria->nome ?? '—' }} |
        <strong>Criada:</strong> {{ $thread->created_at->format('d/m/Y H:i') }}
    </div>

    <div style="border-top:1px solid #e2e8f0;padding-top:16px;">
        <!-- Mensagem original -->
        <div style="margin-bottom:20px;padding:16px;background:#f8fafc;border-radius:8px;">
            <div style="margin-bottom:8px;">
                <strong>{{ $thread->admin->nome ?? 'Admin' }}</strong>
                <small style="color:#888;"> — {{ $thread->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <p>{!! nl2br(e($thread->texto)) !!}</p>
        </div>

        <!-- Respostas -->
        @forelse($respostas as $r)
        <div style="margin-bottom:16px;padding:16px;background:{{ $r->autor_tipo === 'admin' ? '#eff6ff' : '#f0fdf4' }};border-radius:8px;">
            <div style="margin-bottom:8px;">
                <strong>{{ $r->autor_tipo === 'admin' ? ($r->admin->nome ?? 'Admin') : ($r->imobiliaria->nome ?? 'Imobiliária') }}</strong>
                <small style="color:#888;"> — {{ $r->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <p>{!! nl2br(e($r->texto)) !!}</p>
        </div>
        @empty
        <p style="color:#888;text-align:center;padding:20px;">Nenhuma resposta ainda.</p>
        @endforelse
    </div>

    <!-- Responder -->
    <div style="border-top:1px solid #e2e8f0;padding-top:16px;margin-top:16px;">
        <form action="{{ route('admin.mensagens.responder', $thread) }}" method="POST">
            @csrf
            <div class="ul-painel-form-campo">
                <label class="ul-painel-form-label">Responder</label>
                <textarea name="texto" class="ul-painel-form-input" rows="4" required placeholder="Escreva a sua resposta..."></textarea>
                @error('texto')
                    <span class="ul-painel-form-erro">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="ul-painel-btn ul-painel-btn--primario" style="margin-top:8px;">
                <i class="bi bi-send"></i> Enviar Resposta
            </button>
        </form>
    </div>
</div>
@endsection

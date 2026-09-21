@extends('admin.layouts.admin')

@section('title', 'Denúncia #' . $denuncia->id . ' — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <div>
        <h1 class="ul-painel-titulo">Denúncia #{{ $denuncia->id }}</h1>
    </div>
    <a href="{{ route('admin.denuncias') }}" class="ul-painel-btn ul-painel-btn--cinza"><i class="bi bi-arrow-left"></i> Voltar</a>
</div>

<div class="ul-painel-dash-cols">
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="font-size:16px;margin-bottom:14px;">Informações</h3>
        <div class="ul-painel-grid-dados">
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Imóvel</span>
                <span class="ul-painel-dado-valor">{{ $denuncia->imovel->titulo ?? '—' }}</span>
            </div>
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Imobiliária</span>
                <span class="ul-painel-dado-valor">{{ $denuncia->imobiliaria->nome ?? '—' }}</span>
            </div>
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Autor</span>
                <span class="ul-painel-dado-valor">{{ $denuncia->autor_nome }}</span>
            </div>
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Email</span>
                <span class="ul-painel-dado-valor">{{ $denuncia->autor_email }}</span>
            </div>
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Telefone</span>
                <span class="ul-painel-dado-valor">{{ $denuncia->autor_telefone ?? '—' }}</span>
            </div>
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Motivo</span>
                <span class="ul-painel-dado-valor">{{ str_replace('_', ' ', ucfirst($denuncia->motivo)) }}</span>
            </div>
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Estado</span>
                <span class="ul-painel-dado-valor">
                    @if($denuncia->estado === 'pendente')
                        <span class="ul-badge ul-badge--pendente">Pendente</span>
                    @elseif($denuncia->estado === 'em_analise')
                        <span class="ul-badge ul-badge--destaque">Em análise</span>
                    @elseif($denuncia->estado === 'resolvida')
                        <span class="ul-badge ul-badge--aprovado">Resolvida</span>
                    @else
                        <span class="ul-badge ul-badge--cinza">Arquivada</span>
                    @endif
                </span>
            </div>
            <div class="ul-painel-dado">
                <span class="ul-painel-dado-rotulo">Data</span>
                <span class="ul-painel-dado-valor">{{ $denuncia->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div style="margin-top:16px;">
            <strong style="font-size:13px;color:var(--ul-gray2);">Descrição:</strong>
            <p style="margin-top:6px;font-size:14px;color:var(--ul-secondary);">{{ $denuncia->descricao }}</p>
        </div>

        @if($denuncia->resolucao)
        <div style="margin-top:16px;padding:14px;background:#F7F7FD;border-radius:10px;">
            <strong style="font-size:13px;color:var(--ul-gray2);">Resolução:</strong>
            <p style="margin-top:6px;font-size:14px;color:var(--ul-secondary);">{{ $denuncia->resolucao }}</p>
            <small style="color:var(--ul-gray2);">Por {{ $denuncia->resolvidoPor->nome ?? '—' }} em {{ $denuncia->resolvido_em?->format('d/m/Y H:i') }}</small>
        </div>
        @endif
    </div>

    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="font-size:16px;margin-bottom:14px;">Ações</h3>

        @if($denuncia->estado !== 'resolvida' && $denuncia->estado !== 'arquivada')
        <form action="{{ route('admin.denuncias.resolver', $denuncia) }}" method="POST" style="margin-bottom:16px;">
            @csrf
            <div class="ul-painel-form">
                <div class="form-group">
                    <label for="resolucao">Resolução</label>
                    <textarea name="resolucao" id="resolucao" rows="4" placeholder="Descreva como a denúncia foi resolvida..." required></textarea>
                </div>
                <button type="submit" class="ul-painel-btn ul-painel-btn--primario"><i class="bi bi-check-lg"></i> Marcar como Resolvida</button>
            </div>
        </form>

        <form id="denunc-arquivar-{{ $denuncia->id }}" action="{{ route('admin.denuncias.arquivar', $denuncia) }}" method="POST">
            @csrf
            <button type="button" class="ul-painel-btn ul-painel-btn--cinza" onclick="modalConfirmar('Arquivar Denúncia', 'Arquivar esta denúncia?', function(){ document.getElementById('denunc-arquivar-{{ $denuncia->id }}').submit(); })"><i class="bi bi-x-lg"></i> Arquivar</button>
        </form>
        @else
        <p style="color:var(--ul-gray2);font-size:14px;">Esta denúncia já foi {{ $denuncia->estado === 'resolvida' ? 'resolvida' : 'arquivada' }}.</p>
        @endif
    </div>
</div>
@endsection

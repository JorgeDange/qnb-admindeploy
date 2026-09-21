@extends('admin.layouts.admin')

@section('title', 'Subscrições — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Subscrições</h1>
    <a href="{{ route('admin.subscricoes.nova') }}" class="ul-painel-btn ul-painel-btn--primario">
        <i class="bi bi-plus-lg"></i> Nova Subscrição
    </a>
</div>

<div class="ul-painel-card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;">
        <div class="ul-painel-form-campo" style="margin-bottom:0;">
            <label class="ul-painel-form-label">Estado</label>
            <select name="estado" class="ul-painel-form-input">
                <option value="">Todos</option>
                <option value="ativa" {{ request('estado') === 'ativa' ? 'selected' : '' }}>Ativa</option>
                <option value="pendente" {{ request('estado') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="expirada" {{ request('estado') === 'expirada' ? 'selected' : '' }}>Expirada</option>
                <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>
        <div class="ul-painel-form-campo" style="margin-bottom:0;">
            <label class="ul-painel-form-label">Imobiliária</label>
            <select name="imobiliaria_id" class="ul-painel-form-input">
                <option value="">Todas</option>
                @foreach($imobiliarias as $imob)
                    <option value="{{ $imob->id }}" {{ request('imobiliaria_id') == $imob->id ? 'selected' : '' }}>{{ $imob->nome }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="ul-painel-btn ul-painel-btn--cinza">Filtrar</button>
    </form>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>Imobiliária</th>
                <th>Plano</th>
                <th>Início</th>
                <th>Expiração</th>
                <th>Posts</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscricoes as $sub)
            <tr>
                <td>{{ $sub->imobiliaria->nome ?? '—' }}</td>
                <td>{{ $sub->plano->nome ?? '—' }}</td>
                <td>{{ $sub->data_inicio?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    {{ $sub->data_expiracao?->format('d/m/Y') ?? '—' }}
                    @if($sub->data_expiracao && $sub->data_expiracao->isPast())
                        <br><small class="ul-painel-texto-perigo">Expirado</small>
                    @endif
                </td>
                <td>{{ $sub->posts_usados }} / {{ $sub->plano->posts_limite ?? '—' }}</td>
                <td>
                    @php
                        $badgeClass = match($sub->estado) {
                            'ativa' => 'ul-badge--sucesso',
                            'pendente' => 'ul-badge--aviso',
                            'expirada' => 'ul-badge--cinza',
                            'cancelada' => 'ul-badge--perigo',
                            default => 'ul-badge--cinza',
                        };
                    @endphp
                    <span class="ul-badge {{ $badgeClass }}">{{ ucfirst($sub->estado) }}</span>
                </td>
                <td>
                    <div class="ul-painel-imovel-acoes" style="display:inline-flex;gap:6px;align-items:center;">
                        @if($sub->estado === 'ativa' || $sub->estado === 'pendente')
                        <form action="{{ route('admin.subscricoes.renovar', $sub) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="ul-painel-btn ul-painel-btn--pequeno ul-painel-btn--sucesso-texto" title="Renovar">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </form>
                        <form id="sub-cancel-{{ $sub->id }}" action="{{ route('admin.subscricoes.cancelar', $sub) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="button" class="ul-painel-btn ul-painel-btn--pequeno ul-painel-btn--aviso-texto" title="Cancelar" onclick="modalPerigo('Cancelar Subscrição', 'Cancelar esta subscrição?', function(){ document.getElementById('sub-cancel-{{ $sub->id }}').submit(); })">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('admin.subscricoes.editar', $sub) }}" class="ul-painel-btn ul-painel-btn--pequeno" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form id="sub-del-{{ $sub->id }}" action="{{ route('admin.subscricoes.apagar', $sub) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="ul-painel-btn ul-painel-btn--perigo ul-painel-btn--pequeno" title="Apagar" onclick="modalPerigo('Apagar Subscrição', 'Apagar esta subscrição?', function(){ document.getElementById('sub-del-{{ $sub->id }}').submit(); })">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#888;">Nenhuma subscrição encontrada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $subscricoes->withQueryString()->links() }}
@endsection

@extends('admin.layouts.admin')

@section('title', 'Planos — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Planos de Publicação</h1>
    <a href="{{ route('admin.planos.novo') }}" class="ul-painel-btn ul-painel-btn--primario">
        <i class="bi bi-plus-lg"></i> Novo Plano
    </a>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Posts</th>
                <th>Duração</th>
                <th>Subscrições</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($planos as $plano)
            <tr>
                <td>
                    <strong>{{ $plano->nome }}</strong>
                    @if($plano->destaque)
                        <span class="ul-badge ul-badge--destaque">Destaque</span>
                    @endif
                    @if($plano->descricao)
                        <br><small style="color:#888;">{{ Str::limit($plano->descricao, 60) }}</small>
                    @endif
                </td>
                <td>
                    <strong>{{ number_format($plano->preco, 0, ',', '.') }}</strong>
                    <small>{{ $plano->moeda }}</small>
                </td>
                <td>{{ $plano->posts_limite }}</td>
                <td>{{ $plano->dias_validade }} dias</td>
                <td>{{ $plano->imobiliarias_count }}</td>
                <td>
                    <form action="{{ route('admin.planos.toggle', $plano) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="ul-badge {{ $plano->ativo ? 'ul-badge--sucesso' : 'ul-badge--cinza' }}" style="cursor:pointer;border:none;">
                            {{ $plano->ativo ? 'Ativo' : 'Inativo' }}
                        </button>
                    </form>
                </td>
                <td>
                    <div class="ul-painel-imovel-acoes" style="display:inline-flex;gap:6px;align-items:center;">
                        <a href="{{ route('admin.planos.editar', $plano) }}" class="ul-painel-btn ul-painel-btn--pequeno" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form id="plano-del-{{ $plano->id }}" action="{{ route('admin.planos.apagar', $plano) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="ul-painel-btn ul-painel-btn--perigo ul-painel-btn--pequeno" title="Apagar" onclick="modalPerigo('Apagar Plano', 'Tem certeza que deseja apagar este plano?', function(){ document.getElementById('plano-del-{{ $plano->id }}').submit(); })">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#888;">Nenhum plano encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

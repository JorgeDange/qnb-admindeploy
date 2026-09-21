@extends('admin.layouts.admin')

@section('title', 'Avaliações — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Avaliações</h1>
</div>

<div class="ul-painel-stats" style="margin-bottom:20px;">
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['pendente'] }}</span>
        <span class="ul-painel-stat-rotulo">Pendentes</span>
    </div>
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['aprovada'] }}</span>
        <span class="ul-painel-stat-rotulo">Aprovadas</span>
    </div>
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['rejeitada'] }}</span>
        <span class="ul-painel-stat-rotulo">Rejeitadas</span>
    </div>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imóvel</th>
                <th>Autor</th>
                <th>Estrelas</th>
                <th>Comentário</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($avaliacoes as $avaliacao)
            <tr>
                <td><strong>#{{ $avaliacao->id }}</strong></td>
                <td>{{ $avaliacao->imovel->titulo ?? '—' }}</td>
                <td>{{ $avaliacao->autor_nome }}</td>
                <td>
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star" style="font-size:12px;color:{{ $i <= $avaliacao->estrelas ? '#FFA41B' : '#ccc' }};opacity:{{ $i <= $avaliacao->estrelas ? 1 : 0.3 }};"></i>
                    @endfor
                </td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $avaliacao->comentario ?? '—' }}</td>
                <td>
                    @if($avaliacao->estado === 'pendente')
                        <span class="ul-badge ul-badge--pendente">Pendente</span>
                    @elseif($avaliacao->estado === 'aprovada')
                        <span class="ul-badge ul-badge--aprovado">Aprovada</span>
                    @else
                        <span class="ul-badge ul-badge--rejeitado">Rejeitada</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        @if($avaliacao->estado === 'pendente')
                        <form action="{{ route('admin.avaliacoes.aprovar', $avaliacao) }}" method="POST">
                            @csrf
                            <button type="submit" class="ul-painel-btn ul-painel-btn--pequeno ul-painel-btn--sucesso-texto" title="Aprovar"><i class="bi bi-check-lg"></i></button>
                        </form>
                        <form id="aval-rejeitar-{{ $avaliacao->id }}" action="{{ route('admin.avaliacoes.rejeitar', $avaliacao) }}" method="POST">
                            @csrf
                            <button type="button" class="ul-painel-btn ul-painel-btn--pequeno ul-painel-btn--aviso-texto" title="Rejeitar" onclick="modalPerigo('Rejeitar Avaliação', 'Rejeitar esta avaliação?', function(){ document.getElementById('aval-rejeitar-{{ $avaliacao->id }}').submit(); })"><i class="bi bi-x-lg"></i></button>
                        </form>
                        @endif
                        <form id="aval-del-{{ $avaliacao->id }}" action="{{ route('admin.avaliacoes.apagar', $avaliacao) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" class="ul-painel-btn ul-painel-btn--perigo ul-painel-btn--pequeno" title="Apagar" onclick="modalPerigo('Apagar Avaliação', 'Apagar esta avaliação?', function(){ document.getElementById('aval-del-{{ $avaliacao->id }}').submit(); })"><i class="bi bi-x-lg"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#888;">Nenhuma avaliação encontrada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $avaliacoes->withQueryString()->links() }}
@endsection

@extends('admin.layouts.admin')

@section('title', 'Denúncias — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Denúncias</h1>
</div>

<div class="ul-painel-stats" style="margin-bottom:20px;">
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['pendente'] }}</span>
        <span class="ul-painel-stat-rotulo">Pendentes</span>
    </div>
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['em_analise'] }}</span>
        <span class="ul-painel-stat-rotulo">Em análise</span>
    </div>
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['resolvida'] }}</span>
        <span class="ul-painel-stat-rotulo">Resolvidas</span>
    </div>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imóvel</th>
                <th>Autor</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($denuncias as $denuncia)
            <tr>
                <td><strong>#{{ $denuncia->id }}</strong></td>
                <td>{{ $denuncia->imovel->titulo ?? '—' }}</td>
                <td>{{ $denuncia->autor_nome }}</td>
                <td><span class="ul-badge ul-badge--aviso">{{ str_replace('_', ' ', $denuncia->motivo) }}</span></td>
                <td>
                    @if($denuncia->estado === 'pendente')
                        <span class="ul-badge ul-badge--pendente">Pendente</span>
                    @elseif($denuncia->estado === 'em_analise')
                        <span class="ul-badge ul-badge--destaque">Em análise</span>
                    @elseif($denuncia->estado === 'resolvida')
                        <span class="ul-badge ul-badge--aprovado">Resolvida</span>
                    @else
                        <span class="ul-badge ul-badge--cinza">Arquivada</span>
                    @endif
                </td>
                <td>{{ $denuncia->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.denuncias.show', $denuncia) }}" class="ul-painel-btn ul-painel-btn--pequeno" title="Ver detalhes">
                        <i class="bi bi-info-circle"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#888;">Nenhuma denúncia encontrada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $denuncias->withQueryString()->links() }}
@endsection

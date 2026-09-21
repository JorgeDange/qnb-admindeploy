@extends('admin.layouts.admin')

@section('title', 'Visitas — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Visitas</h1>
</div>

<div class="ul-painel-stats" style="margin-bottom:20px;">
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['hoje'] }}</span>
        <span class="ul-painel-stat-rotulo">Hoje</span>
    </div>
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['semana'] }}</span>
        <span class="ul-painel-stat-rotulo">Esta semana</span>
    </div>
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $estatisticas['pendente'] }}</span>
        <span class="ul-painel-stat-rotulo">Pendentes</span>
    </div>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imóvel</th>
                <th>Cliente</th>
                <th>Data/Hora</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visitas as $visita)
            <tr>
                <td><strong>#{{ $visita->id }}</strong></td>
                <td>{{ $visita->imovel->titulo ?? '—' }}</td>
                <td>{{ $visita->cliente_nome }}</td>
                <td>{{ $visita->data_visita->format('d/m/Y H:i') }}</td>
                <td>
                    @if($visita->estado === 'pendente')
                        <span class="ul-badge ul-badge--pendente">Pendente</span>
                    @elseif($visita->estado === 'confirmada')
                        <span class="ul-badge ul-badge--destaque">Confirmada</span>
                    @elseif($visita->estado === 'concluida')
                        <span class="ul-badge ul-badge--aprovado">Concluída</span>
                    @else
                        <span class="ul-badge ul-badge--rejeitado">Cancelada</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        @if($visita->estado === 'pendente')
                        <form action="{{ route('admin.visitas.estado', $visita) }}" method="POST">
                            @csrf
                            <input type="hidden" name="estado" value="confirmada">
                            <button type="submit" class="ul-painel-btn ul-painel-btn--pequeno ul-painel-btn--sucesso-texto" title="Confirmar"><i class="bi bi-check-lg"></i></button>
                        </form>
                        @endif
                        @if($visita->estado === 'confirmada')
                        <form action="{{ route('admin.visitas.estado', $visita) }}" method="POST">
                            @csrf
                            <input type="hidden" name="estado" value="concluida">
                            <button type="submit" class="ul-painel-btn ul-painel-btn--pequeno ul-painel-btn--sucesso-texto" title="Concluir"><i class="bi bi-check-lg"></i></button>
                        </form>
                        @endif
                        @if($visita->estado !== 'cancelada' && $visita->estado !== 'concluida')
                        <form id="visita-cancel-{{ $visita->id }}" action="{{ route('admin.visitas.estado', $visita) }}" method="POST">
                            @csrf
                            <input type="hidden" name="estado" value="cancelada">
                            <button type="button" class="ul-painel-btn ul-painel-btn--perigo ul-painel-btn--pequeno" title="Cancelar" onclick="modalPerigo('Cancelar Visita', 'Cancelar esta visita?', function(){ document.getElementById('visita-cancel-{{ $visita->id }}').submit(); })"><i class="bi bi-x-lg"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#888;">Nenhuma visita encontrada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $visitas->withQueryString()->links() }}
@endsection

@extends('admin.layouts.admin')

@section('title', 'Notificações — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Notificações</h1>
    <form action="{{ route('admin.notificacoes.ler-todas') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="ul-painel-btn ul-painel-btn--cinza">
            <i class="bi bi-check-lg"></i> Marcar todas como lidas
        </button>
    </form>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th style="width:40px;"></th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Título</th>
                <th>Mensagem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notificacoes as $n)
            <tr {{ !$n->lida ? 'class="ul-linha-nao-lida"' : '' }}>
                <td>
                    @if(!$n->lida)
                        <span class="ul-ponto-sucesso"></span>
                    @endif
                </td>
                <td>{{ $n->created_at->format('d/m/Y H:i') }}</td>
                <td><span class="ul-badge ul-badge--cinza">{{ $n->tipo }}</span></td>
                <td>
                    @if($n->url)
                        <a href="{{ route('admin.notificacoes.ler', $n) }}">{{ $n->titulo }}</a>
                    @else
                        {{ $n->titulo }}
                    @endif
                </td>
                <td><small>{{ Str::limit($n->mensagem, 80) }}</small></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:#888;">Nenhuma notificação.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $notificacoes->links() }}
@endsection

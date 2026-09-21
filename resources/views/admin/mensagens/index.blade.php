@extends('admin.layouts.admin')

@section('title', 'Mensagens — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Mensagens Diretas</h1>
    <a href="{{ route('admin.mensagens.nova') }}" class="ul-painel-btn ul-painel-btn--primario">
        <i class="bi bi-plus-lg"></i> Nova Mensagem
    </a>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>Imobiliária</th>
                <th>Assunto</th>
                <th>Enviada por</th>
                <th>Data</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($threads as $t)
            <tr>
                <td><strong>{{ $t->imobiliaria->nome ?? '—' }}</strong></td>
                <td>{{ $t->assunto }}</td>
                <td>{{ $t->admin->nome ?? '—' }}</td>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.mensagens.thread', $t) }}" class="ul-painel-btn ul-painel-btn--pequeno">
                        <i class="bi bi-info-circle"></i> Ver
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:#888;">Nenhuma mensagem.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $threads->links() }}
@endsection

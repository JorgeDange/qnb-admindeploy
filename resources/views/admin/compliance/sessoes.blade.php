@extends('admin.layouts.admin')

@section('title', 'Sessões Ativas — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Sessões Ativas</h1>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>Admin ID</th>
                <th>IP</th>
                <th>User Agent</th>
                <th>Última Atividade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessoes as $s)
            <tr>
                <td>{{ $s->user_id }}</td>
                <td>{{ $s->ip_address ?? '—' }}</td>
                <td><small>{{ Str::limit($s->user_agent, 80) }}</small></td>
                <td>{{ \Carbon\Carbon::createFromTimestamp($s->last_activity)->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;padding:40px;color:#888;">Nenhuma sessão ativa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $sessoes->links() }}
@endsection

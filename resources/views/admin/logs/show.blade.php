@extends('admin.layouts.admin')

@section('title', 'Detalhe do Log — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Detalhe do Log #{{ $log->id }}</h1>
    <a href="{{ route('admin.logs') }}" class="ul-painel-btn ul-painel-btn--cinza">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<div class="ul-painel-card" style="max-width:800px;">
    <table class="ul-painel-tabela" style="margin-bottom:0;">
        <tr>
            <td style="width:180px;"><strong>Data</strong></td>
            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td><strong>Admin</strong></td>
            <td>{{ $log->admin->nome ?? 'Sistema' }} {{ $log->admin ? '(' . $log->admin->email . ')' : '' }}</td>
        </tr>
        <tr>
            <td><strong>Ação</strong></td>
            <td><span class="ul-badge ul-badge--cinza">{{ $log->acao }}</span></td>
        </tr>
        <tr>
            <td><strong>Modelo</strong></td>
            <td>
                @if($log->modelo)
                    {{ $log->modelo }}
                    @if($log->modelo_id)
                        (ID: {{ $log->modelo_id }})
                    @endif
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>IP</strong></td>
            <td>{{ $log->ip_address ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>User Agent</strong></td>
            <td><small>{{ $log->user_agent ?? '—' }}</small></td>
        </tr>
        @if($log->detalhes)
        <tr>
            <td><strong>Detalhes</strong></td>
            <td>
                <pre style="background:#f8f9fa;padding:12px;border-radius:6px;font-size:12px;overflow-x:auto;max-height:400px;">{{ json_encode($log->detalhes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </td>
        </tr>
        @endif
    </table>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'Logs de Atividade — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Logs de Atividade</h1>
</div>

<div class="ul-painel-card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;">
        <div class="ul-painel-form-campo" style="margin-bottom:0;">
            <label class="ul-painel-form-label">Admin</label>
            <select name="admin_id" class="ul-painel-form-input">
                <option value="">Todos</option>
                @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ request('admin_id') == $a->id ? 'selected' : '' }}>{{ $a->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="ul-painel-form-campo" style="margin-bottom:0;">
            <label class="ul-painel-form-label">Ação</label>
            <input type="text" name="acao" class="ul-painel-form-input" value="{{ request('acao') }}" placeholder="Ex: criar_plano">
        </div>
        <div class="ul-painel-form-campo" style="margin-bottom:0;">
            <label class="ul-painel-form-label">Data Início</label>
            <input type="date" name="data_inicio" class="ul-painel-form-input" value="{{ request('data_inicio') }}">
        </div>
        <div class="ul-painel-form-campo" style="margin-bottom:0;">
            <label class="ul-painel-form-label">Data Fim</label>
            <input type="date" name="data_fim" class="ul-painel-form-input" value="{{ request('data_fim') }}">
        </div>
        <button type="submit" class="ul-painel-btn ul-painel-btn--cinza">Filtrar</button>
    </form>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>Data</th>
                <th>Admin</th>
                <th>Ação</th>
                <th>Modelo</th>
                <th>IP</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $log->admin->nome ?? 'Sistema' }}</td>
                <td><span class="ul-badge ul-badge--cinza">{{ $log->acao }}</span></td>
                <td>
                    @if($log->modelo)
                        {{ class_basename($log->modelo) }}
                        @if($log->modelo_id)
                            #{{ $log->modelo_id }}
                        @endif
                    @else
                        —
                    @endif
                </td>
                <td><small>{{ $log->ip_address ?? '—' }}</small></td>
                <td>
                    <a href="{{ route('admin.logs.show', $log) }}" class="ul-painel-btn ul-painel-btn--pequeno" title="Ver detalhe">
                        <i class="bi bi-info-circle"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#888;">Nenhum log encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $logs->withQueryString()->links() }}
@endsection

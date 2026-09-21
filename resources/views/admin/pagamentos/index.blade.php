@extends('admin.layouts.admin')

@section('title', 'Pagamentos — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Pagamentos</h1>
</div>

<div class="ul-painel-card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;align-items:end;">
        <div class="ul-painel-form-campo" style="margin-bottom:0;">
            <label class="ul-painel-form-label">Estado</label>
            <select name="estado" class="ul-painel-form-input">
                <option value="">Todos</option>
                <option value="pendente" {{ request('estado') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="confirmado" {{ request('estado') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                <option value="rejeitado" {{ request('estado') === 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
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
                <th>Valor</th>
                <th>Método</th>
                <th>Estado</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagamentos as $pag)
            <tr>
                <td>{{ $pag->imobiliaria->nome ?? '—' }}</td>
                <td>{{ $pag->subscricao->plano->nome ?? '—' }}</td>
                <td><strong>{{ number_format($pag->valor, 2, ',', '.') }}</strong> {{ $pag->moeda }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $pag->metodo)) }}</td>
                <td>
                    @php
                        $badge = match($pag->estado) {
                            'confirmado' => 'ul-badge--sucesso',
                            'pendente' => 'ul-badge--aviso',
                            'rejeitado' => 'ul-badge--perigo',
                            default => 'ul-badge--cinza',
                        };
                    @endphp
                    <span class="ul-badge {{ $badge }}">{{ ucfirst($pag->estado) }}</span>
                </td>
                <td>{{ $pag->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($pag->estado === 'pendente')
                    <div style="display:inline-flex;gap:6px;">
                        <a href="{{ route('admin.pagamentos.show', $pag) }}" class="ul-painel-btn ul-painel-btn--pequeno">Ver</a>
                    </div>
                    @else
                    <a href="{{ route('admin.pagamentos.show', $pag) }}" class="ul-painel-btn ul-painel-btn--pequeno">Ver</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#888;">Nenhum pagamento encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $pagamentos->links() }}
@endsection

@extends('admin.layouts.admin')

@section('title', 'Faturas — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Faturas</h1>
</div>

<div class="ul-painel-card" style="margin-bottom:15px;">
    <form method="GET" action="{{ route('admin.faturas') }}" style="display:flex;gap:10px;align-items:center;">
        <label style="font-weight:bold;">Filtrar por estado:</label>
        <select name="estado" style="padding:6px 12px;border:1px solid #ddd;border-radius:4px;">
            <option value="">Todos</option>
            @foreach(['pendente', 'aguarda_aprovacao', 'paga', 'rejeitada', 'cancelada', 'expirada'] as $e)
            <option value="{{ $e }}" {{ ($estado ?? '') === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="ul-painel-btn ul-painel-btn--pequeno">Filtrar</button>
        @if($estado)
        <a href="{{ route('admin.faturas') }}" class="ul-painel-btn ul-painel-btn--cinza ul-painel-btn--pequeno">Limpar</a>
        @endif
    </form>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>Número</th>
                <th>Imobiliária</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Data</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($faturas as $f)
            <tr>
                <td><strong>{{ $f->numero }}</strong></td>
                <td>{{ $f->imobiliaria->nome ?? '—' }}</td>
                <td><strong>{{ number_format($f->total, 2, ',', '.') }}</strong> {{ $f->moeda }}</td>
                <td>
                    @php
                        $badge = match($f->estado) {
                            'paga' => 'ul-badge--sucesso',
                            'pendente' => 'ul-badge--aviso',
                            'aguarda_aprovacao' => 'ul-badge--info',
                            'rejeitada' => 'ul-badge--perigo',
                            'cancelada', 'expirada' => 'ul-badge--cinza',
                            default => 'ul-badge--cinza',
                        };
                    @endphp
                    <span class="ul-badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $f->estado)) }}</span>
                </td>
                <td>{{ $f->emitida_em ? $f->emitida_em->format('d/m/Y H:i') : '—' }}</td>
                <td>
                    <a href="{{ route('admin.faturas.show', $f) }}" class="ul-painel-btn ul-painel-btn--pequeno">Ver</a>
                    <a href="{{ route('admin.faturas.download', $f) }}" class="ul-painel-btn ul-painel-btn--pequeno ul-painel-btn--azul">PDF</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#888;">Nenhuma fatura encontrada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $faturas->links() }}
@endsection

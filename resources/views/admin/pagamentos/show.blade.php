@extends('admin.layouts.admin')

@section('title', 'Detalhe do Pagamento — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Pagamento #{{ $pagamento->id }}</h1>
    <a href="{{ route('admin.pagamentos') }}" class="ul-painel-btn ul-painel-btn--cinza">Voltar</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Dados do Pagamento</h3>
            <table class="ul-painel-tabela" style="margin:0;">
                <tr><td style="width:200px;font-weight:bold;">Imobiliária</td><td>{{ $pagamento->imobiliaria->nome ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Plano</td><td>{{ $pagamento->subscricao->plano->nome ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Valor</td><td><strong>{{ number_format($pagamento->valor, 2, ',', '.') }}</strong> {{ $pagamento->moeda }}</td></tr>
                <tr><td style="font-weight:bold;">Método</td><td>{{ ucfirst(str_replace('_', ' ', $pagamento->metodo)) }}</td></tr>
                <tr><td style="font-weight:bold;">Referência</td><td>{{ $pagamento->referencia ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Data Submissão</td><td>{{ $pagamento->created_at->format('d/m/Y H:i') }}</td></tr>
                <tr>
                    <td style="font-weight:bold;">Estado</td>
                    <td>
                        @php
                            $badge = match($pagamento->estado) {
                                'confirmado' => 'ul-badge--sucesso',
                                'pendente' => 'ul-badge--aviso',
                                'rejeitado' => 'ul-badge--perigo',
                                default => 'ul-badge--cinza',
                            };
                        @endphp
                        <span class="ul-badge {{ $badge }}">{{ ucfirst($pagamento->estado) }}</span>
                    </td>
                </tr>
                @if($pagamento->confirmado_em)
                <tr><td style="font-weight:bold;">Confirmado em</td><td>{{ $pagamento->confirmado_em->format('d/m/Y H:i') }}</td></tr>
                <tr><td style="font-weight:bold;">Confirmado por</td><td>{{ $pagamento->confirmadoPor->name ?? '—' }}</td></tr>
                @endif
                @if($pagamento->motivo_rejeicao)
                <tr><td style="font-weight:bold;">Motivo Rejeição</td><td class="ul-painel-texto-perigo">{{ $pagamento->motivo_rejeicao }}</td></tr>
                @endif
            </table>
        </div>

        @if($pagamento->comprovativo)
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Comprovativo</h3>
            <div style="padding:10px 0;">
                <img src="{{ asset('storage/' . $pagamento->comprovativo) }}" alt="Comprovativo" style="max-width:100%;max-height:500px;border-radius:8px;border:1px solid #eee;">
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if($pagamento->fatura)
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Fatura Gerada</h3>
            <table class="ul-painel-tabela" style="margin:0;">
                <tr><td style="font-weight:bold;">Número</td><td>{{ $pagamento->fatura->numero }}</td></tr>
                <tr><td style="font-weight:bold;">Total</td><td>{{ number_format($pagamento->fatura->total, 2, ',', '.') }} {{ $pagamento->fatura->moeda }}</td></tr>
                <tr><td style="font-weight:bold;">Emitida</td><td>{{ $pagamento->fatura->emitida_em ? $pagamento->fatura->emitida_em->format('d/m/Y') : '—' }}</td></tr>
            </table>
        </div>
        @endif

        @if($pagamento->estado === 'pendente')
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Ações</h3>
            <p style="color:#888;font-size:13px;">Gerir pagamento via Fatura:</p>
            @if($pagamento->fatura)
            <a href="{{ route('admin.faturas.show', $pagamento->fatura) }}" class="ul-painel-btn ul-painel-btn--azul" style="width:100%;">Ver Fatura #{{ $pagamento->fatura->numero }}</a>
            @else
            <p style="color:#e74c3c;">Fatura não encontrada.</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

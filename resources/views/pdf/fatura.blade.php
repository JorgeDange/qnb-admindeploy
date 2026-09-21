<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { background-color: #1a5276; color: #fff; padding: 20px 30px; margin-bottom: 30px; }
        .header h1 { font-size: 20px; margin-bottom: 5px; }
        .header p { font-size: 11px; opacity: 0.8; }
        .fatura-info { display: flex; justify-content: space-between; margin-bottom: 25px; padding: 0 10px; }
        .fatura-info .bloco { width: 48%; }
        .fatura-info .bloco h3 { font-size: 11px; color: #1a5276; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .fatura-info .bloco p { margin-bottom: 3px; font-size: 11px; }
        .fatura-numero { text-align: right; }
        .fatura-numero h2 { font-size: 22px; color: #1a5276; margin-bottom: 5px; }
        .fatura-numero p { font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #1a5276; color: #fff; padding: 8px 12px; text-align: left; font-size: 11px; }
        td { padding: 8px 12px; border-bottom: 1px solid #eee; font-size: 11px; }
        .totais { width: 300px; margin-left: auto; }
        .totais tr td { padding: 6px 12px; }
        .totais tr:last-child td { border-top: 2px solid #1a5276; font-weight: bold; font-size: 13px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
        .notas { margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 4px; }
        .notas h4 { font-size: 11px; color: #1a5276; margin-bottom: 5px; }
        .notas p { font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $empresa['empresa_nome'] ?? 'QNB Imobiliaria' }}</h1>
        <p>NIF: {{ $empresa['empresa_nif'] ?? '' }} | {{ $empresa['empresa_endereco'] ?? '' }}</p>
        @if($empresa['empresa_telefone'] ?? null)
        <p>Tel: {{ $empresa['empresa_telefone'] }} | Email: {{ $empresa['empresa_email'] ?? '' }}</p>
        @endif
    </div>

    <div class="fatura-info">
        <div class="bloco">
            <h3>Faturar A</h3>
            <p><strong>{{ $fatura->imobiliaria->nome ?? '—' }}</strong></p>
            <p>{{ $fatura->imobiliaria->email ?? '' }}</p>
            <p>{{ $fatura->imobiliaria->telefone ?? '' }}</p>
            <p>{{ $fatura->imobiliaria->provincia ?? '' }}, {{ $fatura->imobiliaria->municipio ?? '' }}</p>
        </div>
        <div class="bloco fatura-numero">
            <h2>FATURA</h2>
            <p><strong>No:</strong> {{ $fatura->numero }}</p>
            <p><strong>Data:</strong> {{ $fatura->emitida_em ? $fatura->emitida_em->format('d/m/Y') : now()->format('d/m/Y') }}</p>
            <p><strong>Estado:</strong> {{ $fatura->pagamento->estado === 'confirmado' ? 'Paga' : 'Pendente' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descricao</th>
                <th style="text-align:center;">Qtd</th>
                <th style="text-align:right;">Valor Unit.</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fatura->linhas as $linha)
            <tr>
                <td>{{ $linha->descricao }}</td>
                <td style="text-align:center;">{{ $linha->quantidade }}</td>
                <td style="text-align:right;">{{ number_format($linha->valor_unitario, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($linha->subtotal, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td>
                    <strong>Plano: {{ $fatura->pagamento->subscricao->plano->nome ?? '—' }}</strong><br>
                    <span style="font-size:10px;color:#666;">
                        Validade: {{ $fatura->pagamento->subscricao->plano->dias_validade ?? 0 }} dias |
                        Posts: {{ $fatura->pagamento->subscricao->plano->posts_limite ?? 0 }} imoveis
                    </span>
                </td>
                <td style="text-align:center;">1</td>
                <td style="text-align:right;">{{ number_format($fatura->valor, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($fatura->valor, 2, ',', '.') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totais">
        <tr>
            <td>Subtotal:</td>
            <td style="text-align:right;">{{ number_format($fatura->subtotal, 2, ',', '.') }} {{ $fatura->moeda }}</td>
        </tr>
        @php
            $ivaPerc = $configFatura['iva_percentagem'] ?? 14;
            $retPerc = $configFatura['retencao_percentagem'] ?? 0;
        @endphp
        <tr>
            <td>IVA ({{ $ivaPerc }}%):</td>
            <td style="text-align:right;">{{ number_format($fatura->iva, 2, ',', '.') }} {{ $fatura->moeda }}</td>
        </tr>
        @if($fatura->desconto > 0)
        <tr>
            <td>Desconto:</td>
            <td style="text-align:right;">-{{ number_format($fatura->desconto, 2, ',', '.') }} {{ $fatura->moeda }}</td>
        </tr>
        @endif
        @if($fatura->retencao > 0)
        <tr>
            <td>Retencao na Fonte ({{ $retPerc }}%):</td>
            <td style="text-align:right;">-{{ number_format($fatura->retencao, 2, ',', '.') }} {{ $fatura->moeda }}</td>
        </tr>
        @endif
        <tr>
            <td>Total:</td>
            <td style="text-align:right;">{{ number_format($fatura->total, 2, ',', '.') }} {{ $fatura->moeda }}</td>
        </tr>
    </table>

    <div class="notas">
        <h4>Dados Bancarios</h4>
        <p><strong>Banco:</strong> {{ $empresa['banco_nome'] ?? '—' }} | <strong>IBAN:</strong> {{ $empresa['banco_iban'] ?? '—' }} | <strong>Titular:</strong> {{ $empresa['banco_titular'] ?? '—' }}</p>
    </div>

    @if($fatura->nota)
    <div class="notas" style="margin-top:10px;">
        <h4>Nota</h4>
        <p>{{ $fatura->nota }}</p>
    </div>
    @endif

    <div class="footer">
        <p>{{ $empresa['empresa_nome'] ?? 'QNB Imobiliaria' }} — {{ $empresa['empresa_endereco'] ?? '' }}</p>
        @if($configFatura['rodape'] ?? null)
        <p>{{ $configFatura['rodape'] }}</p>
        @endif
    </div>
</body>
</html>

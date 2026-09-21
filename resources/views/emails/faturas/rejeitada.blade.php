<!DOCTYPE html>
<html lang="pt">
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;color:#333;max-width:600px;margin:0 auto;">
    <div style="background:#e74c3c;color:#fff;padding:20px;text-align:center;">
        <h1 style="margin:0;font-size:18px;">QNB Imobiliária</h1>
    </div>
    <div style="padding:20px;">
        <h2>Comprovativo Rejeitado</h2>
        <p>O comprovativo submetido para a fatura <strong>{{ $fatura->numero }}</strong> foi rejeitado.</p>
        <table style="width:100%;border-collapse:collapse;margin:15px 0;">
            <tr><td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Fatura</td><td style="padding:8px;border-bottom:1px solid #eee;">{{ $fatura->numero }}</td></tr>
            <tr><td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Valor</td><td style="padding:8px;border-bottom:1px solid #eee;">{{ number_format($fatura->total, 2, ',', '.') }} {{ $fatura->moeda }}</td></tr>
            <tr><td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Motivo</td><td style="padding:8px;border-bottom:1px solid #eee;color:#e74c3c;">{{ $fatura->motivo_rejeicao ?? 'Não especificado' }}</td></tr>
        </table>
        <p>Por favor, submeta um novo comprovativo de pagamento.</p>
        <p style="color:#888;font-size:12px;margin-top:30px;">Este é um email automático. Não responda.</p>
    </div>
</body>
</html>

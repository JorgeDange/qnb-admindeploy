<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $estado === 'aprovado' ? 'Imóvel Aprovado' : 'Imóvel Rejeitado' }} — QNB Imobiliária</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;">
        <div style="background-color: {{ $estado === 'aprovado' ? '#27ae60' : '#c0392b' }}; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 22px;">QNB Imobiliária</h1>
        </div>
        <div style="padding: 30px;">
            @if($estado === 'aprovado')
                <h2 style="color: #27ae60; margin-top: 0;">Imóvel Aprovado</h2>
                <p>O imóvel abaixo foi <strong style="color: #27ae60;">aprovado</strong> e já está visível na plataforma.</p>
            @else
                <h2 style="color: #c0392b; margin-top: 0;">Imóvel Rejeitado</h2>
                <p>O imóvel abaixo foi <strong style="color: #c0392b;">rejeitado</strong>. Consulte o painel para mais detalhes.</p>
            @endif

            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Referência</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $imovel->referencia }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Título</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $imovel->titulo }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Tipo</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $imovel->tipo }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Preço</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ number_format($imovel->preco, 2, ',', '.') }} {{ $imovel->moeda }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Estado</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <span style="color: {{ $estado === 'aprovado' ? '#27ae60' : '#c0392b' }}; font-weight: bold;">
                            {{ ucfirst($estado) }}
                        </span>
                    </td>
                </tr>
            </table>

            <p>Acesse o painel para gerenciar os seus imóveis.</p>
        </div>
        <div style="background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #999;">
            &copy; {{ date('Y') }} QNB Imobiliária. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>

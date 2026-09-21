<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #27ae60; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 22px;">QNB Imobiliária</h1>
        </div>
        <div style="padding: 30px;">
            <h2 style="color: #27ae60; margin-top: 0;">Assinatura Liberada</h2>
            <p>A sua assinatura foi liberada com sucesso! Já pode começar a utilizar a plataforma.</p>

            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Plano</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $assinatura->plano->nome }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Data de Início</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $assinatura->data_inicio->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Data de Expiração</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $assinatura->data_expiracao ? $assinatura->data_expiracao->format('d/m/Y') : 'Indefinido' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">Posts Disponíveis</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $assinatura->plano->posts_limite - $assinatura->posts_usados }} de {{ $assinatura->plano->posts_limite }}</td>
                </tr>
            </table>

            <p>Acesse o painel para gerir os seus imóveis.</p>
        </div>
        <div style="background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #999;">
            &copy; {{ date('Y') }} QNB Imobiliária. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>

<?php

namespace App\Helpers;

class FormatHelper
{
    public static function maskTelefone(string $telefone): string
    {
        $only = preg_replace('/\D/', '', $telefone);

        if (str_starts_with($only, '244')) {
            $only = substr($only, 3);
        }

        if (strlen($only) === 9) {
            return '+244 ' . substr($only, 0, 3) . ' ' . substr($only, 3, 3) . ' ' . substr($only, 6);
        }

        return '+244 ' . $telefone;
    }

    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '****@****.com';
        }

        $user = $parts[0];
        $domain = $parts[1];

        $maskedUser = substr($user, 0, 2) . str_repeat('*', max(0, strlen($user) - 2));
        $domainParts = explode('.', $domain);
        $maskedDomain = str_repeat('*', strlen($domainParts[0] ?? ''));

        return $maskedUser . '@' . $maskedDomain . '.' . ($domainParts[1] ?? 'com');
    }
}

if (!function_exists('mask')) {
    function mask(string $value, string $type = 'telefone'): string
    {
        return match ($type) {
            'email' => \App\Helpers\FormatHelper::maskEmail($value),
            default => \App\Helpers\FormatHelper::maskTelefone($value),
        };
    }
}

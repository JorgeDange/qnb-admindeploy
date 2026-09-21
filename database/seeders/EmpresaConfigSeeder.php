<?php

namespace Database\Seeders;

use App\Models\EmpresaConfig;
use Illuminate\Database\Seeder;

class EmpresaConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Identificação
            ['chave' => 'empresa_nome',        'valor' => 'QNB Imobiliária, Lda',     'grupo' => 'identificacao'],
            ['chave' => 'empresa_nif',         'valor' => '5417892140',               'grupo' => 'identificacao'],
            ['chave' => 'empresa_endereco',    'valor' => 'Luanda, Angola',           'grupo' => 'identificacao'],

            // Contacto
            ['chave' => 'empresa_telefone',    'valor' => '+244 921 852 727',         'grupo' => 'contacto'],
            ['chave' => 'empresa_email',       'valor' => 'comercial@qnbangola.com',  'grupo' => 'contacto'],
            ['chave' => 'empresa_website',     'valor' => 'https://qnbangola.com',    'grupo' => 'contacto'],

            // Identidade
            ['chave' => 'empresa_logo',        'valor' => null,                       'grupo' => 'identidade'],

            // Bancário
            ['chave' => 'banco_nome',          'valor' => 'BFA',                      'grupo' => 'bancario'],
            ['chave' => 'banco_iban',          'valor' => 'AO06 0040 0000 8857 2019 1019 5', 'grupo' => 'bancario'],
            ['chave' => 'banco_conta',         'valor' => '8857201910195',            'grupo' => 'bancario'],
            ['chave' => 'banco_titular',       'valor' => 'QNB Imobiliária, Lda',     'grupo' => 'bancario'],
            ['chave' => 'banco_nif',           'valor' => '5417892140',               'grupo' => 'bancario'],

            // Fatura
            ['chave' => 'fatura_termos',              'valor' => 'Pagamento a efetuar por transferência bancária ou depósito, no prazo de {dias} dias úteis.', 'grupo' => 'fatura'],
            ['chave' => 'fatura_rodape',              'valor' => 'Este documento não substitui uma fatura oficial emitida pelo contribuinte.', 'grupo' => 'fatura'],
            ['chave' => 'fatura_iva_percentagem',     'valor' => '14',                 'grupo' => 'fatura'],
            ['chave' => 'fatura_retencao_percentagem', 'valor' => '0',                 'grupo' => 'fatura'],
            ['chave' => 'fatura_dias_vencimento',     'valor' => '30',                 'grupo' => 'fatura'],
        ];

        foreach ($configs as $config) {
            EmpresaConfig::updateOrCreate(
                ['chave' => $config['chave']],
                $config
            );
        }
    }
}

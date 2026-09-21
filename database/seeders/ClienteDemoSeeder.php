<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteDemoSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::updateOrCreate(
            ['email' => 'cliente@qnbangola.com'],
            [
                'nome'              => 'Cliente Demo',
                'telefone'          => '+244 921 852 727',
                'password'          => bcrypt('cliente1234'),
                'email_verificado_em' => now(),
                'estado'            => 'ativo',
                'aceitou_termos_em' => now(),
                'aceitou_termos_ip' => '127.0.0.1',
            ]
        );
    }
}

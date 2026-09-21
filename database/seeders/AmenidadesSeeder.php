<?php

namespace Database\Seeders;

use App\Models\Amenidade;
use Illuminate\Database\Seeder;

class AmenidadesSeeder extends Seeder
{
    public function run(): void
    {
        $amenidades = [
            'Ar Condicionado', 'Estacionamento', 'Piscina', 'Segurança 24h', 'Elevador',
            'Cozinha Equipada', 'Ginásio', 'Churrasqueira', 'Wi-Fi', 'Smart TV',
            'Varanda', 'Jardim', 'Área de Serviço', 'Mobiliado', 'Gerador',
        ];

        foreach ($amenidades as $nome) {
            Amenidade::updateOrCreate(['nome' => $nome], ['ativo' => true]);
        }
    }
}

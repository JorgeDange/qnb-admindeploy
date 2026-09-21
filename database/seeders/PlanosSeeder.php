<?php

namespace Database\Seeders;

use App\Models\Plano;
use Illuminate\Database\Seeder;

class PlanosSeeder extends Seeder
{
    public function run(): void
    {
        $planos = [
            ['nome' => 'Básico', 'posts_limite' => 5, 'dias_validade' => 30, 'preco' => 25000, 'moeda' => 'Kz'],
            ['nome' => 'Profissional', 'posts_limite' => 10, 'dias_validade' => 30, 'preco' => 45000, 'moeda' => 'Kz'],
            ['nome' => 'Premium', 'posts_limite' => 25, 'dias_validade' => 60, 'preco' => 90000, 'moeda' => 'Kz'],
        ];

        foreach ($planos as $plano) {
            Plano::updateOrCreate(['nome' => $plano['nome']], $plano);
        }
    }
}

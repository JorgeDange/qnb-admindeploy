<?php

namespace Database\Factories;

use App\Models\Imovel;
use App\Models\Imobiliaria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImovelFactory extends Factory
{
    protected $model = Imovel::class;

    public function definition(): array
    {
        return [
            'imobiliaria_id' => Imobiliaria::factory(),
            'referencia'     => 'IMV-' . strtoupper(uniqid()),
            'titulo'         => $this->faker->sentence(3),
            'tipo'           => $this->faker->randomElement(['apartamento', 'vivenda', 'terreno', 'loja', 'escritorio']),
            'finalidade'     => $this->faker->randomElement(['arrendar', 'comprar', 'vender']),
            'preco'          => $this->faker->randomFloat(2, 5000000, 500000000),
            'moeda'          => 'Kz',
            'provincia'      => 'Luanda',
            'municipio'      => 'Maianga',
            'descricao'      => $this->faker->paragraph(),
            'estado'         => 'aprovado',
            'disponibilidade' => 'disponivel',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Mensagem;
use App\Models\Imobiliaria;
use App\Models\Imovel;
use Illuminate\Database\Eloquent\Factories\Factory;

class MensagemFactory extends Factory
{
    protected $model = Mensagem::class;

    public function definition(): array
    {
        return [
            'imobiliaria_id' => Imobiliaria::factory(),
            'imovel_id'      => null,
            'nome'           => $this->faker->name(),
            'contacto'       => $this->faker->phoneNumber(),
            'texto'          => $this->faker->paragraph(),
            'origem'         => 'imovel',
            'lida'           => false,
        ];
    }
}

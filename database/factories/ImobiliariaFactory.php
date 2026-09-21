<?php

namespace Database\Factories;

use App\Models\Imobiliaria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ImobiliariaFactory extends Factory
{
    protected $model = Imobiliaria::class;

    public function definition(): array
    {
        return [
            'nome'     => $this->faker->company(),
            'nif'      => $this->faker->unique()->numerify('#########'),
            'email'    => $this->faker->unique()->safeEmail(),
            'telefone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password123'),
            'estado'   => 'aprovada',
        ];
    }
}

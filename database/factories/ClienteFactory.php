<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'telefone'          => $this->faker->phoneNumber(),
            'password'          => Hash::make('cliente1234'),
            'email_verificado_em' => now(),
            'estado'            => 'ativo',
            'aceitou_termos_em' => now(),
            'aceitou_termos_ip' => $this->faker->ipv4(),
        ];
    }

    public function naoVerificado(): static
    {
        return $this->state(fn () => ['email_verificado_em' => null]);
    }

    public function suspenso(): static
    {
        return $this->state(fn () => ['estado' => 'suspenso']);
    }
}

<?php

namespace Database\Factories;

use App\Models\Reserva;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservaFactory extends Factory
{
    protected $model = Reserva::class;

    public function definition()
    {
        return [
            'origem' => $this->faker->city(),
            'destino' => $this->faker->city(),
            'tipo' => $this->faker->randomElement(['aerea', 'rodoviaria']),
            'ida' => $this->faker->dateTimeBetween('now', '+1 month'),
            'volta' => $this->faker->optional()->dateTimeBetween('+1 month', '+2 months'),
            'motivo' => $this->faker->sentence(),
            'validacao' => $this->faker->randomElement(['Aprovado', 'Pendente']),
            'email_gestor' => $this->faker->safeEmail(),
            'observacoes' => $this->faker->optional()->text(),
            'nome' => $this->faker->name(),
            'cpf' => $this->faker->numerify('###########'),
            'rg' => $this->faker->numerify('#########'),
            'data_nascimento' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail(),
            // Associe a reserva a um usuário existente ou crie um novo usuário
            'user_id' => User::factory(),
            'user_name' => $this->faker->name(),
            'user_cpf' => $this->faker->numerify('###########'),
            'user_email' => $this->faker->unique()->safeEmail(),
        ];
    }
}

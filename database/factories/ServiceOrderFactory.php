<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceOrder>
 */
class ServiceOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $entryDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $exitDate = $this->faker->optional(0.6)->dateTimeBetween($entryDate, '+1 day');
        
        return [
            'vehiclePlate' => strtoupper($this->faker->regexify('[A-Z]{3}[0-9]{4}')),
            'entryDateTime' => $entryDate,
            'exitDateTime' => $exitDate ?: '0001-01-01 00:00:00',
            'priceType' => $this->faker->optional()->randomElement(['hora', 'diaria', 'mensal', 'avulso']),
            'price' => $this->faker->randomFloat(2, 0, 999.99),
            'userId' => User::factory()
        ];
    }
}

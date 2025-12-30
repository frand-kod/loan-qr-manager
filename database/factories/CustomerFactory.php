<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Otomatis buat user jika tidak dipassing
            'name' => $this->faker->name(),
            'whatsapp_number' => '628'.$this->faker->numerify('##########'),
            'customer_flag' => 'aman',
        ];
    }
}

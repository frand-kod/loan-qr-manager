<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'amount' => 100000,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'reference_id' => 'INV-'.strtoupper(str()->random(10)),
            'status' => 'pending',
        ];
    }
}

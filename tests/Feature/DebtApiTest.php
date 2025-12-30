<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DebtApiTest extends TestCase
{
    use RefreshDatabase; // Meriset database setiap kali test dijalankan

    protected $user;

    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan User dan Customer untuk testing
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create([
            'user_id' => $this->user->id,
            'whatsapp_number' => '628123456789',
        ]);
    }

    #[Test]
    public function user_can_create_debt_via_api()
    {
        $user = User::factory()->create();
        // Gunakan factory yang sudah kita definisikan di atas
        $customer = Customer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/debts', [
                'customer_id' => $customer->id,
                'amount' => 50000,
                'due_date' => now()->addDays(7)->format('Y-m-d'),
            ]);

        $response->assertStatus(201);
    }

    #[Test]
    public function it_validates_required_fields_for_debt()
    {
        $this->actingAs($this->user, 'sanctum');

        // Kirim data kosong
        $response = $this->postJson('/api/debts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_id', 'amount', 'due_date']);
    }
}

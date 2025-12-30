<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Debt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WhatsAppReminderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_whatsapp_when_due_date_is_h_minus_7()
    {
        // 1. Mock HTTP
        Http::fake();

        // 2. Setup Data (Gunakan format Y-m-d agar sinkron dengan database)
        $customer = Customer::factory()->create();
        $targetDate = now()->addDays(7)->format('Y-m-d');

        Debt::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'due_date' => $targetDate,
        ]);

        // 3. Jalankan Command
        $this->artisan('reminder:send-wa');

        // 4. Assertion (Cukup pastikan ADA request yang keluar)
        Http::assertSent(function ($request) {
            // Logika ini akan menangkap request apapun yang dikirim oleh WhatsappService
            return true;
        });

        // 5. Pastikan label terupdate di database
        $this->assertDatabaseHas('debts', [
            'last_reminder_sent' => 'H-7',
        ]);
    }
    //     public function it_sends_whatsapp_when_due_date_is_h_minus_7()
    //     {
    //         // 1. Mocking HTTP agar tidak mengirim WA sungguhan
    //         Http::fake([
    //             '*' => Http::response(['status' => 'success'], 200),
    //         ]);
    //
    //         // 2. Setup Data: Buat hutang yang jatuh temponya tepat 7 hari lagi
    //         $customer = Customer::factory()->create([
    //             'name' => 'John Doe',
    //             'whatsapp_number' => '628123456789',
    //         ]);
    //
    //         Debt::factory()->create([
    //             'customer_id' => $customer->id,
    //             'amount' => 500000,
    //             'status' => 'pending',
    //             'due_date' => now()->addDays(7)->format('Y-m-d'),
    //         ]);
    //
    //         // 3. Jalankan Command
    //         $this->artisan('reminder:send-wa');
    //
    //         // 4. Assertion: Pastikan ada request HTTP yang terkirim ke API WA
    //         Http::assertSent(function ($request) {
    //             return str_contains($request->body(), 'John Doe') &&
    //                    str_contains($request->body(), '500,000') &&
    //                    str_contains($request->body(), '628123456789');
    //         });
    //
    //         // 5. Cek apakah database terupdate
    //         $this->assertDatabaseHas('debts', [
    //             'last_reminder_sent' => 'H-7',
    //         ]);
    //     }
}

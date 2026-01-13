<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function createQrisTransaction(Debt $debt)
    {
        $url = config('services.tripay.mode') === 'production'
            ? config('services.tripay.production_url')
            : config('services.tripay.sanbox_url');

        $payload = [
            'method' => 'QRIS2', // Sesuaikan kode di Tripay
            'merchant_ref' => $debt->reference_id,
            'amount' => $debt->amount,
            'customer_name' => $debt->customer->name,
            'customer_email' => 'customer@pituangku.com', // Dummy email jika tidak ada
            'customer_phone' => $debt->customer->whatsapp_number,
            'order_items' => [
                [
                    'name' => 'Pelunasan Piutang - '.$debt->reference_id,
                    'price' => $debt->amount,
                    'quantity' => 1,
                ],
            ],
            'signature' => hash_hmac('sha256', config('services.tripay.merchant_code').$debt->reference_id.$debt->amount, config('services.tripay.private_key')),
        ];

        $response = Http::withToken(config('services.tripay.api_key'))
            ->withoutVerifying()
            ->post($url, $payload);

        // If a Guzzle promise was returned (e.g., async), wait and decode; otherwise use Laravel response json()
        if ($response instanceof \GuzzleHttp\Promise\PromiseInterface) {
            $guzzleResponse = $response->wait();
            $body = (string) $guzzleResponse->getBody();

            return json_decode($body, true);
        }

        return $response->json();
    }

    public function handleWebhook(object $data)
    {
        // Catat data yang masuk ke storage/logs/laravel.log
        \Log::info('Tripay Callback Masuk:', $data->all());
        // Mencari hutang berdasarkan reference_id yang dikirim Tripay
        $debt = Debt::where('reference_id', $data->merchant_ref)->first();

        // Cek jika data ada dan status dari Tripay adalah PAID
        if ($debt && $data->status === 'PAID') {

            // Cek agar tidak terjadi double input jika webhook terpanggil 2 kali
            if ($debt->status === 'paid') {
                return;
            }

            $debt->update(['status' => 'paid']);

            $payment = Payment::create([
                'debt_id' => $debt->id,
                'amount_paid' => $data->total_amount,
                'tripay_reference' => $data->reference,
                'paid_at' => now(),
            ]);

            $this->logService->record(
                "Pembayaran lunas via QRIS: {$debt->reference_id}",
                'Payment',
                $payment->id
            );
        }
    }
}

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
            ? 'https://tripay.co.id/api/transaction/create' // Gunakan URL langsung jika config bermasalah
            : 'https://tripay.co.id/api-sandbox/transaction/create';

        $merchantCode = config('services.tripay.merchant_code');
        $privateKey = config('services.tripay.private_key');
        $amount = $debt->remaining_amount; // Gunakan sisa hutang, bukan total awal
        $merchantRef = $debt->reference_id.'-'.time(); // Tambahkan time agar unik jika generate ulang

        // RUMUS SIGNATURE TRIPAY UNTUK TRANSAKSI
        $signature = hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey);

        $payload = [
            'method' => 'QRIS2',
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $debt->customer->name,
            'customer_email' => $debt->customer->email ?? 'customer@pituangku.com',
            'customer_phone' => $debt->customer->whatsapp_number,
            'order_items' => [
                [
                    'name' => 'Pelunasan Piutang #'.$debt->reference_id,
                    'price' => $amount,
                    'quantity' => 1,
                ],
            ],
            'signature' => $signature,
        ];

        $response = Http::withToken(config('services.tripay.api_key'))
            ->withoutVerifying()
            ->post($url, $payload);

        $res = $response->json();

        // Tambahkan format response agar Controller tidak bingung
        if ($response->successful() && isset($res['success']) && $res['success'] == true) {
            return [
                'success' => true,
                'data' => [
                    'qr_url' => $res['data']['qr_url'] ?? ($res['data']['checkout_url'] ?? null),
                    'amount' => $res['data']['amount'],
                    'merchant_ref' => $res['data']['merchant_ref'],
                    'reference' => $res['data']['reference'],
                ],
            ];
        }

        return [
            'success' => false,
            'message' => $res['message'] ?? 'Terjadi kesalahan pada API Tripay',
        ];
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

    public function handleManualPayment(Debt $debt)
    {
        if ($debt->status === 'paid') {
            return;
        }

        // 1. Simpan data pembayaran sebagai "CASH"
        $debt->payment()->create([
            'amount_paid' => $debt->remaining_amount, // Masukkan sisa hutang
            'tripay_reference' => 'MANUAL',                // Gunakan kolom ini untuk info referensi
            'paid_at' => now(),                   // Isi timestamp saat ini
        ]);

        // Jangan lupa update status hutangnya juga
        $debt->update([
            'remaining_amount' => 0,
            'status' => 'paid',
        ]);

        // 3. Catat log
        $this->logService->record(
            "Pelunasan manual oleh Admin untuk: {$debt->reference_id}",
            'Payment',
            $debt->id
        );
    }

    public function checkTransactionStatus(Debt $debt)
    {
        // Cari record payment terakhir untuk hutang ini yang masih 'UNPAID'
        $payment = Payment::where('debt_id', $debt->id)
            ->where('status', 'UNPAID')
            ->latest()
            ->first();

        if (! $payment) {
            return ['success' => false, 'message' => 'Tidak ada transaksi aktif'];
        }

        $apiKey = config('services.tripay.api_key');
        $payload = ['reference' => $payment->reference];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_URL => config('services.tripay.base_url').'transaction/detail?'.http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ["Authorization: Bearer $apiKey"],
            CURLOPT_FAILONERROR => false,
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $result = json_decode($response);

        if ($result && $result->success && $result->data->status === 'PAID') {
            // Jika di Tripay sudah PAID, update database kita
            $payment->update(['status' => 'PAID']);
            $debt->update([
                'remaining_amount' => 0,
                'status' => 'paid',
            ]);

            return ['success' => true, 'status' => 'PAID'];
        }

        return ['success' => true, 'status' => 'UNPAID'];
    }
}

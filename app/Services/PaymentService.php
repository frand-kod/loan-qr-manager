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

        $response = Http::withToken(config('services.tripay.api_key'))->post($url, $payload);

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
                'payment_method' => 'tripay',
                'status' => 'completed'
            ]);

            $this->logService->record(
                "Pembayaran lunas via QRIS: {$debt->reference_id}",
                'Payment',
                $payment->id
            );
        }
    }

    /**
     * Create a manual payment record
     */
    public function createPayment(array $data)
    {
        // Verify the debt belongs to the authenticated user
        $debt = Debt::where('id', $data['debt_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $payment = Payment::create([
            'debt_id' => $data['debt_id'],
            'amount_paid' => $data['amount_paid'],
            'payment_method' => $data['payment_method'] ?? 'cash',
            'reference_number' => $data['reference_number'] ?? null,
            'paid_at' => $data['paid_at'] ?? now(),
            'status' => $data['status'] ?? 'completed'
        ]);

        // Update debt status if payment is completed and covers the full amount
        $this->updateDebtStatus($debt, $payment);

        // Log the activity
        $this->logService->record(
            "Payment of Rp " . number_format($payment->amount_paid) . " recorded for debt #{$debt->reference_id}",
            'Payment',
            $payment->id
        );

        return $payment;
    }

    /**
     * Update an existing payment
     */
    public function updatePayment(Payment $payment, array $data)
    {
        $oldAmount = $payment->amount_paid;

        $payment->update($data);

        // Update debt status if amount changed
        if (isset($data['amount_paid']) && $data['amount_paid'] != $oldAmount) {
            $this->updateDebtStatus($payment->debt, $payment);
        }

        // Log the activity
        $this->logService->record(
            "Payment updated: Rp " . number_format($oldAmount) . " → Rp " . number_format($payment->amount_paid),
            'Payment',
            $payment->id
        );

        return $payment;
    }

    /**
     * Delete a payment
     */
    public function deletePayment(Payment $payment)
    {
        $debt = $payment->debt;
        $amount = $payment->amount_paid;

        $payment->delete();

        // Update debt status after payment deletion
        $this->updateDebtStatusAfterDeletion($debt);

        // Log the activity
        $this->logService->record(
            "Payment deleted: Rp " . number_format($amount) . " for debt #{$debt->reference_id}",
            'Payment',
            $payment->id
        );
    }

    /**
     * Update debt status based on payments
     */
    private function updateDebtStatus(Debt $debt, Payment $payment = null)
    {
        $totalPaid = Payment::where('debt_id', $debt->id)
            ->where('status', 'completed')
            ->sum('amount_paid');

        if ($totalPaid >= $debt->amount) {
            $debt->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $debt->update(['status' => 'pending']);
        }
        // If total paid is 0, keep current status
    }

    /**
     * Update debt status after payment deletion
     */
    private function updateDebtStatusAfterDeletion(Debt $debt)
    {
        $totalPaid = Payment::where('debt_id', $debt->id)
            ->where('status', 'completed')
            ->sum('amount_paid');

        if ($totalPaid == 0) {
            $debt->update(['status' => 'pending']);
        } elseif ($totalPaid >= $debt->amount) {
            $debt->update(['status' => 'paid']);
        } else {
            $debt->update(['status' => 'pending']);
        }
    }

    /**
     * Get payment statistics for a user
     */
    public function getPaymentStatistics($userId)
    {
        $totalReceived = Payment::byUser($userId)
            ->completed()
            ->sum('amount_paid');

        $totalPending = Payment::byUser($userId)
            ->pending()
            ->sum('amount_paid');

        $paymentCount = Payment::byUser($userId)->count();

        $completedCount = Payment::byUser($userId)->completed()->count();

        $thisMonth = Payment::byUser($userId)
            ->completed()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount_paid');

        return [
            'total_received' => $totalReceived,
            'total_pending' => $totalPending,
            'payment_count' => $paymentCount,
            'completed_count' => $completedCount,
            'this_month' => $thisMonth,
        ];
    }

    /**
     * Get payments by customer
     */
    public function getPaymentsByCustomer($customerId, $userId)
    {
        return Payment::with(['debt'])
            ->whereHas('debt', function($query) use ($customerId, $userId) {
                $query->where('customer_id', $customerId)
                      ->where('user_id', $userId);
            })
            ->orderBy('paid_at', 'desc')
            ->get();
    }
}

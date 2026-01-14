<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request; // Tambahkan ini
use Inertia\Inertia;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = Payment::with(['debt.customer'])
            ->whereHas('debt', function ($q) {
                $q->where('user_id', auth()->id());
            })->latest()->paginate(10);

        return Inertia::render('Payment', ['payments' => $payments]);
    }

    public function payQris(Debt $debt)
    {
        $result = $this->paymentService->createQrisTransaction($debt);

        if ($result['success']) {
            // Data ini yang akan ditangkap oleh Watcher di Index.vue
            return back()->with('qris_data', [
                'debt_id' => $debt->id,
                'qr_url' => $result['data']['qr_url'],
                'amount' => $result['data']['amount'],
                'merchant_ref' => $result['data']['merchant_ref'],
                'reference' => $result['data']['reference'],
            ]);
        }

        return back()->with('error', 'Gagal generate QRIS: '.$result['message']);
    }

    public function handleWebhook(Request $request)
    {
        // 1. Verifikasi Signature Tripay (Opsional tapi disarankan)
        $callbackSignature = $request->header('X-Callback-Signature');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, config('services.tripay.private_key'));

        if ($signature !== $callbackSignature) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        // 2. Ambil data JSON
        $data = json_decode($json);

        // 3. Panggil fungsi di SERVICE (Bukan $this->handleWebhook karena akan loop)
        $this->paymentService->handleWebhook($data);

        return response()->json(['success' => true]);
    }

    public function checkStatus(Debt $debt)
    {
        // Panggil service untuk cek status ke API Tripay
        $result = $this->paymentService->checkTransactionStatus($debt);

        if ($result['success'] && $result['status'] === 'PAID') {
            return back()->with('success', 'Pembayaran berhasil dikonfirmasi! Status telah diperbarui.');
        }

        return back()->with('error', 'Pembayaran belum terdeteksi. Silakan tunggu beberapa saat atau hubungi admin.');
    }
}

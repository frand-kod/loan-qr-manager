<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\Payment;
use App\Services\PaymentService;
use Inertia\Inertia;

class PaymentController extends Controller
{
    protected $payment;

    public function __construct(PaymentService $payment)
    {
        $this->payment = $payment;
    }

    public function index()
    {
        $payments = Payment::with(['debt.customer'])
            ->whereHas('debt', function ($q) {
                $q->where('user_id', auth()->id());
            })->latest()->paginate(10);

        return Inertia::render('Payments/Index', ['payments' => $payments]);
    }

    public function payQris(Debt $debt)
    {
        $result = $this->payment->createQrisTransaction($debt);

        if ($result['success']) {
            // Kita kirim URL instruksi/QRIS ke Frontend
            return back()->with('qris_data', $result['data']);
        }

        return back()->with('error', 'Gagal generate QRIS: '.$result['message']);
    }

    public function handleWebhook($data)
    {
        return $this->handleWebhook($data);
    }
}

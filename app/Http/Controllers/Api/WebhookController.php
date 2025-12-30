<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    //
    public function handleTripay(Request $request, PaymentService $paymentService)
    {
        // 1. Ambil Signature dari Header
        $callbackSignature = $request->header('X-Callback-Signature');

        // 2. Ambil JSON mentah untuk divalidasi signaturenya
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, config('services.tripay.private_key'));

        // 3. Validasi Keamanan
        if ($signature !== $callbackSignature) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Signature',
            ], 403);
        }

        // 4. Konversi request menjadi object dan kirim ke Service
        $data = (object) $request->all();
        $paymentService->handleWebhook($data);

        return response()->json(['success' => true]);
    }
}

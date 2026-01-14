<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use Inertia\Inertia;

class PublicPaymentController extends Controller
{
    //
    public function show($reference, $hash)
    {
        $debt = Debt::with('customer')->where('reference_id', $reference)->firstOrFail();

        // Validasi hash untuk keamanan
        if ($hash !== md5($debt->id.config('app.key'))) {
            abort(403, 'Link tidak valid atau kadaluarsa.');
        }

        if ($debt->status === 'paid') {
            return Inertia::render('Public/AlreadyPaid', ['debt' => $debt]);
        }

        return Inertia::render('Public/PaymentPage', [
            'debt' => $debt,
        ]);
    }
}

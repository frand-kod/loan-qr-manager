<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = Payment::with(['debt.customer'])
            ->byUser($userId)
            ->orderBy('paid_at', 'desc');

        // Apply filters
        if ($request->has('customer_id') && $request->customer_id) {
            $query->whereHas('debt', function($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Payment history retrieved successfully',
            'data' => $payments,
        ], 200);
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'debt_id' => 'required|exists:debts,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,transfer,tripay,bank_transfer',
            'reference_number' => 'nullable|string|max:255',
            'paid_at' => 'nullable|date',
            'status' => 'required|in:pending,completed,failed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payment = $this->paymentService->createPayment($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => $payment->load(['debt.customer']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        // Check if payment belongs to authenticated user
        if ($payment->debt->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to payment',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $payment->load(['debt.customer']),
        ], 200);
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment)
    {
        // Check if payment belongs to authenticated user
        if ($payment->debt->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to payment',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount_paid' => 'sometimes|numeric|min:0.01',
            'payment_method' => 'sometimes|in:cash,transfer,tripay,bank_transfer',
            'reference_number' => 'nullable|string|max:255',
            'paid_at' => 'nullable|date',
            'status' => 'sometimes|in:pending,completed,failed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payment = $this->paymentService->updatePayment($payment, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => $payment->load(['debt.customer']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Payment $payment)
    {
        // Check if payment belongs to authenticated user
        if ($payment->debt->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to payment',
            ], 403);
        }

        try {
            $this->paymentService->deletePayment($payment);

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function statistics()
    {
        $userId = auth()->id();

        $stats = $this->paymentService->getPaymentStatistics($userId);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ], 200);
    }
}
